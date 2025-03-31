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
    public function toggleComplete(int $id): JsonResponse
    {
        $task = $this->model->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();
        
        if (!$task) {
            return $this->notFoundResponse(__('messages.task.not_found'));
        }
        
        $task->completed = !$task->completed;
        $task->completed_at = $task->completed ? now() : null;
        $task->save();
        
        if (!empty($this->defaultRelations)) {
            $task->load($this->defaultRelations);
        }
        
        return $this->successResponse($task, __('messages.task.status_updated'));
    }

    /**
     * Get user's task statistics.
     */
    public function getStatistics(): JsonResponse
    {
        $userId = auth()->id();
        
        $stats = [
            'total' => $this->model->where('user_id', $userId)->count(),
            'completed' => $this->model->where('user_id', $userId)->where('completed', true)->count(),
            'pending' => $this->model->where('user_id', $userId)->where('completed', false)->count(),
            'overdue' => $this->model->where('user_id', $userId)
                ->where('completed', false)
                ->where('due_date', '<', now()->format('Y-m-d'))
                ->count(),
            'upcoming' => $this->model->where('user_id', $userId)
                ->where('completed', false)
                ->where('due_date', '>=', now()->format('Y-m-d'))
                ->count(),
            'by_priority' => [
                'low' => $this->model->where('user_id', $userId)->where('priority', 0)->count(),
                'medium' => $this->model->where('user_id', $userId)->where('priority', 1)->count(),
                'high' => $this->model->where('user_id', $userId)->where('priority', 2)->count(),
            ],
            'by_category' => $this->model->where('user_id', $userId)
                ->selectRaw('category_id, count(*) as count')
                ->groupBy('category_id')
                ->with('category:id,name,color')
                ->get()
                ->pluck('count', 'category.name')
                ->toArray(),
        ];
        
        return $this->successResponse($stats);
    }

    /**
     * Get the allowed relations for this service.
     */
    protected function getAllowedRelations(): array
    {
        return ['category', 'user'];
    }
} 