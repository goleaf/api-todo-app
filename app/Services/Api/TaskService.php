<?php

namespace App\Services\Api;

use App\Models\Task;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    use ApiResponse;

    /**
     * Display a listing of tasks with filtering capabilities.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $query = Task::forUser($userId);

        // Apply filters
        if ($request->has('category_id')) {
            $query->inCategory($request->category_id);
        }

        if ($request->has('completed')) {
            $completed = filter_var($request->completed, FILTER_VALIDATE_BOOLEAN);
            $query = $completed ? $query->completed() : $query->incomplete();
        }

        if ($request->has('priority')) {
            $query->withPriority($request->priority);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('due_date')) {
            $query->dueOn($request->due_date);
        }

        if ($request->has('tag')) {
            $query->withTag($request->tag);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        if ($sortBy === 'priority') {
            $query->orderByPriority();
        } elseif ($sortBy === 'due_date') {
            $query->orderByDueDate();
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $tasks = $query->paginate($perPage);

        return $this->successResponse($tasks);
    }

    /**
     * Store a new task.
     */
    public function store(array $data): JsonResponse
    {
        $data['user_id'] = Auth::id();

        $task = Task::create($data);

        return $this->createdResponse($task, __('messages.task.created'));
    }

    /**
     * Display a specific task.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $userId = Auth::id();
        $task = Task::forUser($userId)->find($id);

        if (! $task) {
            return $this->errorResponse(__('validation.task.not_found'), 404);
        }

        return $this->successResponse($task);
    }

    /**
     * Update a task.
     */
    public function update(int $id, array $data): JsonResponse
    {
        $userId = Auth::id();
        $task = Task::forUser($userId)->find($id);

        if (! $task) {
            return $this->errorResponse(__('validation.task.not_found'), 404);
        }

        $task->update($data);

        return $this->successResponse($task, __('messages.task.updated'));
    }

    /**
     * Delete a task.
     */
    public function destroy(int $id): JsonResponse
    {
        $userId = Auth::id();
        $task = Task::forUser($userId)->find($id);

        if (! $task) {
            return $this->errorResponse(__('validation.task.not_found'), 404);
        }

        $task->delete();

        return $this->noContentResponse(__('messages.task.deleted'));
    }

    /**
     * Toggle a task's completion status.
     */
    public function toggleCompletion(int $id): JsonResponse
    {
        $userId = Auth::id();
        $task = Task::forUser($userId)->find($id);

        if (! $task) {
            return $this->errorResponse(__('validation.task.not_found'), 404);
        }

        $task->toggleCompletion();

        $message = $task->completed
            ? __('messages.task.marked_complete')
            : __('messages.task.marked_incomplete');

        return $this->successResponse($task, $message);
    }

    /**
     * Get tasks due today.
     */
    public function getDueToday(): JsonResponse
    {
        $userId = Auth::id();
        $tasks = Task::forUser($userId)->dueToday()->get();

        return $this->successResponse($tasks);
    }

    /**
     * Get overdue tasks.
     */
    public function getOverdue(): JsonResponse
    {
        $userId = Auth::id();
        $tasks = Task::forUser($userId)->overdue()->get();

        return $this->successResponse($tasks);
    }

    /**
     * Get upcoming tasks.
     */
    public function getUpcoming(): JsonResponse
    {
        $userId = Auth::id();
        $days = request('days', 7);
        $tasks = Task::forUser($userId)->upcoming($days)->get();

        return $this->successResponse($tasks);
    }

    /**
     * Get task statistics.
     */
    public function getStatistics(): JsonResponse
    {
        $userId = Auth::id();
        $user = Auth::user();

        return $this->successResponse($user->getTaskStatistics());
    }
}
