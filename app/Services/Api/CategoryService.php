<?php

namespace App\Services\Api;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    use ApiResponse;

    /**
     * Display a listing of categories with filtering capabilities.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $query = Category::forUser($userId);

        // Apply filters
        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('type')) {
            $type = CategoryType::fromValueOrDefault($request->type);
            $query->where('type', $type ? $type->value : CategoryType::OTHER->value);
        }

        if ($request->has('has_tasks')) {
            $hasTasksFlag = filter_var($request->has_tasks, FILTER_VALIDATE_BOOLEAN);
            if ($hasTasksFlag) {
                $query->withTasks();
            }
        }

        if ($request->has('has_incomplete_tasks')) {
            $hasIncompleteTasksFlag = filter_var($request->has_incomplete_tasks, FILTER_VALIDATE_BOOLEAN);
            if ($hasIncompleteTasksFlag) {
                $query->withIncompleteTasks();
            }
        }

        if ($request->has('tag')) {
            $query->withTag($request->tag);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDir = $request->get('sort_dir', 'asc');

        if ($sortBy === 'name') {
            $query->orderByName();
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        // Add task counts
        $query->withCount(['tasks', 'tasks as completed_task_count' => function ($query) {
            $query->where('completed', true);
        }]);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $categories = $query->paginate($perPage);

        return $this->successResponse($categories);
    }

    /**
     * Store a new category.
     */
    public function store(array $data): JsonResponse
    {
        $data['user_id'] = Auth::id();

        // Ensure type is set
        if (!isset($data['type'])) {
            $data['type'] = CategoryType::OTHER->value;
        }

        $category = Category::create($data);

        return $this->createdResponse($category, __('messages.category.created'));
    }

    /**
     * Display a specific category.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $userId = Auth::id();
        $category = Category::forUser($userId)->find($id);

        if (! $category) {
            return $this->errorResponse(__('validation.category.not_found'), 404);
        }

        return $this->successResponse($category);
    }

    /**
     * Update a category.
     */
    public function update(int $id, array $data): JsonResponse
    {
        $userId = Auth::id();
        $category = Category::forUser($userId)->find($id);

        if (! $category) {
            return $this->errorResponse(__('validation.category.not_found'), 404);
        }

        $category->update($data);

        return $this->successResponse($category, __('messages.category.updated'));
    }

    /**
     * Delete a category.
     */
    public function destroy(int $id): JsonResponse
    {
        $userId = Auth::id();
        $category = Category::forUser($userId)->find($id);

        if (! $category) {
            return $this->errorResponse(__('validation.category.not_found'), 404);
        }

        // Check if category has tasks
        $taskCount = $category->tasks()->count();
        if ($taskCount > 0) {
            return $this->errorResponse(
                __('messages.category.has_tasks', ['count' => $taskCount]),
                422
            );
        }

        $category->delete();

        return $this->noContentResponse(__('messages.category.deleted'));
    }

    /**
     * Get task counts for each category.
     */
    public function getTaskCounts(): JsonResponse
    {
        $userId = Auth::id();
        $categories = Category::forUser($userId)
            ->withCount(['tasks', 'tasks as completed_tasks_count' => function ($query) {
                $query->where('completed', true);
            }])
            ->get()
            ->each(function ($category) {
                $category->completion_percentage = $category->tasks_count > 0
                    ? round(($category->completed_tasks_count / $category->tasks_count) * 100, 1)
                    : 0;
            });

        return $this->successResponse($categories);
    }
}
