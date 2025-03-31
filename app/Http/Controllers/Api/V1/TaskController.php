<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Models\Task;
use App\Traits\ApiResponses;
use App\Traits\LogsErrors;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Throwable;

class TaskController extends Controller
{
    use ApiResponses, LogsErrors;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $cacheKey = 'tasks_' . $userId . '_' . md5(json_encode($request->all()));
            
            // Check if we have a cached result
            if (!$request->has('skip_cache') && Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            
            $query = Task::with(['category'])
                ->where('user_id', $userId);

            // Filter by status
            if ($request->has('status')) {
                $status = $request->status;
                if ($status === 'completed') {
                    $query->where('completed', true);
                } elseif ($status === 'pending') {
                    $query->where('completed', false);
                } elseif ($status === 'overdue') {
                    $query->overdue();
                } elseif ($status === 'due_today') {
                    $query->dueToday();
                } elseif ($status === 'upcoming') {
                    $query->upcoming();
                }
            }

            // Filter by category
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by due date
            if ($request->has('due') && $request->due) {
                $due = $request->due;
                if ($due === 'today') {
                    $query->dueToday();
                } elseif ($due === 'tomorrow') {
                    $query->whereDate('due_date', now()->addDay());
                } elseif ($due === 'week') {
                    $query->dueThisWeek();
                }
            }

            // Filter by tags
            if ($request->has('tags') && $request->tags) {
                $tags = is_array($request->tags) ? $request->tags : [$request->tags];
                foreach ($tags as $tag) {
                    $query->withTag($tag);
                }
            }
            
            // Filter by reminder
            if ($request->has('has_reminder') && $request->boolean('has_reminder')) {
                $query->withReminders();
            }

            // Search
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->search($search);
            }

            // Filter by priority
            if ($request->has('priority')) {
                $priority = $this->convertPriorityToInteger($request->priority);
                $query->byPriority($priority);
            }

            // Order by
            $orderBy = $request->input('order_by', 'created_at');
            $orderDir = $request->input('order_dir', 'desc');

            // Validate the order_by field to prevent SQL injection
            $allowedOrderFields = [
                'created_at', 'due_date', 'priority', 'title', 
                'updated_at', 'completed_at', 'progress'
            ];
            if (! in_array($orderBy, $allowedOrderFields)) {
                $orderBy = 'created_at';
            }

            // Pagination
            $perPage = $request->input('per_page', 15);
            $tasks = $query->orderBy($orderBy, $orderDir)->paginate($perPage);

            $response = $this->paginatedResponse(
                paginator: $tasks,
                message: 'Tasks retrieved successfully'
            );
            
            // Cache the response for 5 minutes
            if (!$request->has('skip_cache')) {
                Cache::put($cacheKey, $response, now()->addMinutes(5));
            }
            
            return $response;
        } catch (Throwable $e) {
            $this->logError($e, ['request' => $request->all()]);

            return $this->serverErrorResponse('Failed to retrieve tasks');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $userId = Auth::id();

            $task = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'priority' => $this->convertPriorityToInteger($validated['priority'] ?? 'medium'),
                'completed' => false,
                'user_id' => $userId,
                'session_id' => $validated['session_id'] ?? null,
                'reminder_at' => $validated['reminder_at'] ?? null,
                'tags' => $validated['tags'] ?? [],
                'progress' => $validated['progress'] ?? 0,
            ]);

            $task->load('category');

            // Broadcast event
            event(new TaskCreated($task));
            
            // Clear task cache for this user
            $this->clearTaskCache($userId);

            return $this->createdResponse(
                data: $task,
                message: 'Task created successfully'
            );
        } catch (Throwable $e) {
            $this->logError($e, ['request' => $request->all()]);

            return $this->serverErrorResponse('Failed to create task');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        try {
            $this->authorize('view', $task);
            
            $cacheKey = 'task_' . $task->id;
            
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = $this->successResponse(
                data: $task->load('category'),
                message: 'Task retrieved successfully'
            );
            
            Cache::put($cacheKey, $response, now()->addMinutes(30));
            
            return $response;
        } catch (Throwable $e) {
            $this->logError($e, ['task_id' => $task->id]);

            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return $this->forbiddenResponse('You are not authorized to view this task');
            }

            return $this->serverErrorResponse('Failed to retrieve task');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        try {
            $this->authorize('update', $task);

            $validated = $request->validated();

            // Convert string priority to integer if present
            if (isset($validated['priority'])) {
                $validated['priority'] = $this->convertPriorityToInteger($validated['priority']);
            }

            // Handle completed state
            if (isset($validated['completed']) && $validated['completed']) {
                $validated['completed_at'] = now();
                $validated['progress'] = 100;
            } elseif (isset($validated['completed']) && ! $validated['completed']) {
                $validated['completed_at'] = null;
            }

            $task->update($validated);
            $task->load('category');

            // Broadcast event
            event(new TaskUpdated($task));
            
            // Clear relevant cache entries
            $this->clearTaskCache($task->user_id);
            Cache::forget('task_' . $task->id);

            return $this->successResponse(
                data: $task,
                message: 'Task updated successfully'
            );
        } catch (Throwable $e) {
            $this->logError($e, [
                'task_id' => $task->id,
                'request' => $request->all(),
            ]);

            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return $this->forbiddenResponse('You are not authorized to update this task');
            }

            return $this->serverErrorResponse('Failed to update task');
        }
    }

    /**
     * Toggle the completed status of a task.
     */
    public function toggleComplete(Task $task): JsonResponse
    {
        try {
            $this->authorize('update', $task);

            $wasCompleted = $task->completed;
            
            if ($wasCompleted) {
                $task->markAsIncomplete();
                // For task status changes to incomplete, we use the standard TaskUpdated event
                event(new TaskUpdated($task));
            } else {
                $task->markAsComplete();
                // Task was just completed, dispatch TaskCompleted event
                event(new \App\Events\TaskCompleted($task));
            }

            $task->load('category');
            
            // Clear relevant cache entries
            $this->clearTaskCache($task->user_id);
            Cache::forget('task_' . $task->id);

            return $this->successResponse(
                data: $task,
                message: 'Task status toggled successfully'
            );
        } catch (Throwable $e) {
            $this->logError($e, ['task_id' => $task->id]);

            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return $this->forbiddenResponse('You are not authorized to update this task');
            }

            return $this->serverErrorResponse('Failed to update task status');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        try {
            $this->authorize('delete', $task);

            $taskData = $task->toArray();
            $userId = $task->user_id;

            $task->delete();

            // Broadcast event
            event(new TaskDeleted($taskData, $userId));
            
            // Clear cache
            $this->clearTaskCache($userId);
            Cache::forget('task_' . $task->id);

            return $this->successResponse(
                data: null,
                message: 'Task deleted successfully'
            );
        } catch (Throwable $e) {
            $this->logError($e, ['task_id' => $task->id]);

            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                return $this->forbiddenResponse('You are not authorized to delete this task');
            }

            return $this->serverErrorResponse('Failed to delete task');
        }
    }

    /**
     * Convert string priority to integer.
     */
    private function convertPriorityToInteger(string $priority): int
    {
        return match (strtolower($priority)) {
            'low' => 0,
            'medium' => 1,
            'high' => 2,
            default => (int) $priority,
        };
    }
    
    /**
     * Clear all task-related cache entries for a user
     */
    private function clearTaskCache(int $userId): void
    {
        $cacheKeys = Cache::get('user_' . $userId . '_task_cache_keys', []);
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        // Also clear any keys that match the pattern tasks_{userid}_*
        $pattern = 'tasks_' . $userId . '_*';
        $cache = Cache::getStore();
        if (method_exists($cache, 'deletePattern')) {
            $cache->deletePattern($pattern);
        }
        
        // Clear the dashboard cache
        Cache::forget('dashboard_' . $userId);
    }
}
