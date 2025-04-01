<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Task\StoreTaskRequest;
use App\Http\Requests\Api\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Task::where('user_id', auth()->id())
            ->with(['category', 'tags']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by tags
        if ($request->has('tag_ids')) {
            $tagIds = explode(',', $request->tag_ids);
            $query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            });
        }

        // Filter by due date
        if ($request->has('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }

        // Search in title and description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort options
        $sortField = $request->get('sort_by', 'due_date');
        $sortDirection = $request->get('sort_direction', 'asc');
        $allowedSortFields = ['title', 'due_date', 'priority', 'created_at', 'status'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
        }

        $tasks = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $tasks->items(),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total()
            ]
        ]);
    }

    /**
     * Store a newly created task.
     *
     * @param StoreTaskRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTaskRequest $request)
    {
        DB::beginTransaction();
        try {
            $task = new Task();
            $task->user_id = auth()->id();
            $task->title = $request->title;
            $task->description = $request->description;
            $task->due_date = $request->due_date;
            $task->priority = $request->priority;
            $task->status = $request->status ?? 'todo';
            $task->category_id = $request->category_id;
            $task->save();

            // Attach tags if provided
            if ($request->has('tag_ids')) {
                $task->tags()->attach($request->tag_ids);
            }

            $task->load(['category', 'tags']);

            DB::commit();

            return response()->json([
                'message' => 'Task created successfully',
                'data' => $task
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create task',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified task.
     *
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        $task->load(['category', 'tags', 'timeEntries', 'attachments']);

        return response()->json([
            'data' => $task
        ]);
    }

    /**
     * Update the specified task.
     *
     * @param UpdateTaskRequest $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        DB::beginTransaction();
        try {
            $task->title = $request->title ?? $task->title;
            $task->description = $request->description ?? $task->description;
            $task->due_date = $request->due_date ?? $task->due_date;
            $task->priority = $request->priority ?? $task->priority;
            $task->status = $request->status ?? $task->status;
            $task->category_id = $request->category_id ?? $task->category_id;
            $task->save();

            // Sync tags if provided
            if ($request->has('tag_ids')) {
                $task->tags()->sync($request->tag_ids);
            }

            $task->load(['category', 'tags']);

            DB::commit();

            return response()->json([
                'message' => 'Task updated successfully',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update task',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified task.
     *
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        DB::beginTransaction();
        try {
            // Delete related records
            $task->timeEntries()->delete();
            $task->attachments()->delete();
            $task->tags()->detach();
            $task->delete();

            DB::commit();

            return response()->json([
                'message' => 'Task deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete task',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the task status.
     *
     * @param Request $request
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'status' => ['required', 'string', 'in:todo,in_progress,completed']
        ]);

        $task->status = $request->status;
        $task->save();

        return response()->json([
            'message' => 'Task status updated successfully',
            'data' => $task
        ]);
    }
}
