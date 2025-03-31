<?php

namespace App\Services\Api;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskService extends ApiService
{
    /**
     * TaskService constructor.
     */
    public function __construct(Task $model)
    {
        $this->model = $model;
        $this->defaultRelations = ['category', 'user'];
    }

    /**
     * Override the buildIndexQuery method to apply user filtering.
     */
    protected function buildIndexQuery(Request $request): Builder
    {
        $query = parent::buildIndexQuery($request);
        
        // Only show tasks belonging to the authenticated user
        $query->where('user_id', auth()->id());
        
        // Filter by completion status if requested
        if ($request->has('completed')) {
            $completed = filter_var($request->get('completed'), FILTER_VALIDATE_BOOLEAN);
            $query->where('completed', $completed);
        }
        
        // Filter by priority if requested
        if ($request->has('priority')) {
            $query->where('priority', $request->get('priority'));
        }
        
        // Filter by category if requested
        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }
        
        // Filter by due date range if requested
        if ($request->has('due_date_from') && $request->has('due_date_to')) {
            $query->whereBetween('due_date', [
                $request->get('due_date_from'),
                $request->get('due_date_to')
            ]);
        } elseif ($request->has('due_date_from')) {
            $query->where('due_date', '>=', $request->get('due_date_from'));
        } elseif ($request->has('due_date_to')) {
            $query->where('due_date', '<=', $request->get('due_date_to'));
        }
        
        // Filter by specific types
        if ($request->has('type')) {
            $type = $request->get('type');
            
            switch ($type) {
                case 'today':
                    $query->dueToday();
                    break;
                case 'overdue':
                    $query->overdue();
                    break;
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'no_due_date':
                    $query->whereNull('due_date');
                    break;
                // Add more types as needed
            }
        }
        
        // Filter by tags if requested
        if ($request->has('tag')) {
            $tag = $request->get('tag');
            $query->withTag($tag);
        }
        
        // Default sorting
        $sort = $request->get('sort_by', 'due_date');
        $direction = $request->get('sort_direction', 'asc');
        
        if ($sort === 'priority') {
            $query->orderBy('priority', 'desc'); // Higher priority first
        } else {
            $query->orderBy($sort, $direction);
        }
        
        return $query;
    }

    /**
     * Override the store method to add the user_id.
     */
    public function store(array $validatedData): JsonResponse
    {
        // Add the authenticated user ID
        $validatedData['user_id'] = auth()->id();
        
        return parent::store($validatedData);
    }

    /**
     * Toggle the completion status of a task.
     */
    public function toggleCompletion(int $id): JsonResponse
    {
        $task = $this->model->where('user_id', auth()->id())->find($id);
        
        if (!$task) {
            return $this->notFoundResponse(__('messages.not_found'));
        }
        
        $task->toggleCompletion();
        
        return $this->successResponse($task, __('messages.task.toggled'));
    }

    /**
     * Get tasks that are due today.
     */
    public function getDueToday(): JsonResponse
    {
        $tasks = $this->model
            ->where('user_id', auth()->id())
            ->dueToday()
            ->with($this->defaultRelations)
            ->orderByPriority()
            ->get();
        
        return $this->successResponse($tasks);
    }

    /**
     * Get overdue tasks.
     */
    public function getOverdue(): JsonResponse
    {
        $tasks = $this->model
            ->where('user_id', auth()->id())
            ->overdue()
            ->with($this->defaultRelations)
            ->orderByPriority()
            ->get();
        
        return $this->successResponse($tasks);
    }

    /**
     * Get upcoming tasks.
     */
    public function getUpcoming(int $days = 7): JsonResponse
    {
        $tasks = $this->model
            ->where('user_id', auth()->id())
            ->upcoming($days)
            ->with($this->defaultRelations)
            ->orderBy('due_date')
            ->orderByPriority()
            ->get();
        
        return $this->successResponse($tasks);
    }

    /**
     * Get task statistics.
     */
    public function getStatistics(): JsonResponse
    {
        $userId = auth()->id();
        $today = now()->format('Y-m-d');
        
        $totalCount = $this->model->where('user_id', $userId)->count();
        $completedCount = $this->model->where('user_id', $userId)->completed()->count();
        $overdueCount = $this->model->where('user_id', $userId)
            ->where('completed', false)
            ->where('due_date', '<', $today)
            ->count();
        $todayCount = $this->model->where('user_id', $userId)
            ->where('due_date', $today)
            ->count();
        $upcomingCount = $this->model->where('user_id', $userId)
            ->where('completed', false)
            ->where('due_date', '>', $today)
            ->count();
        
        // Get tasks by priority
        $byPriority = [
            'low' => $this->model->where('user_id', $userId)->where('priority', 1)->count(),
            'medium' => $this->model->where('user_id', $userId)->where('priority', 2)->count(),
            'high' => $this->model->where('user_id', $userId)->where('priority', 3)->count(),
            'urgent' => $this->model->where('user_id', $userId)->where('priority', 4)->count(),
        ];
        
        // Get tasks by category
        $byCategory = $this->model
            ->where('user_id', $userId)
            ->whereNotNull('category_id')
            ->selectRaw('category_id, count(*) as count')
            ->groupBy('category_id')
            ->with('category:id,name,color')
            ->get()
            ->map(function ($item) {
                return [
                    'category_id' => $item->category_id,
                    'name' => $item->category->name,
                    'color' => $item->category->color,
                    'count' => $item->count,
                ];
            });
        
        // Completion rate
        $completionRate = $totalCount > 0 ? round(($completedCount / $totalCount) * 100, 1) : 0;
        
        return $this->successResponse([
            'total' => $totalCount,
            'completed' => $completedCount,
            'overdue' => $overdueCount,
            'today' => $todayCount,
            'upcoming' => $upcomingCount,
            'completion_rate' => $completionRate,
            'by_priority' => $byPriority,
            'by_category' => $byCategory,
        ]);
    }

    /**
     * Get the allowed relations for this service.
     */
    protected function getAllowedRelations(): array
    {
        return ['category', 'user'];
    }
} 