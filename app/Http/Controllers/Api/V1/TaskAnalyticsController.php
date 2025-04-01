<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Task;
use App\Services\Api\TaskAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskAnalyticsController extends Controller
{
    protected TaskAnalyticsService $analyticsService;

    /**
     * Constructor to inject dependencies.
     *
     * @param TaskAnalyticsService $analyticsService
     */
    public function __construct(TaskAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get comments for the authenticated user's tasks.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserTaskComments(Request $request): JsonResponse
    {
        $user = $request->user();
        $comments = $this->analyticsService->getUserTaskComments($user);

        return response()->json([
            'success' => true,
            'data' => $comments,
            'message' => 'Task comments retrieved successfully',
        ]);
    }

    /**
     * Get tags used across the authenticated user's tasks.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserTaskTags(Request $request): JsonResponse
    {
        $user = $request->user();
        $tags = $this->analyticsService->getUserTaskTags($user);

        return response()->json([
            'success' => true,
            'data' => $tags,
            'message' => 'Task tags retrieved successfully',
        ]);
    }

    /**
     * Get engagement metrics for a specific task.
     *
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     */
    public function getTaskEngagementMetrics(Request $request, Task $task): JsonResponse
    {
        // Ensure the user can access this task
        $this->authorize('view', $task);

        $metrics = $this->analyticsService->getTaskEngagementMetrics($task);

        return response()->json([
            'success' => true,
            'data' => $metrics,
            'message' => 'Task engagement metrics retrieved successfully',
        ]);
    }

    /**
     * Get tasks from the authenticated user's categories.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserCategoryTasks(Request $request): JsonResponse
    {
        $user = $request->user();
        $tasks = $this->analyticsService->getUserCategoryTasks($user);

        return response()->json([
            'success' => true,
            'data' => $tasks,
            'message' => 'Category tasks retrieved successfully',
        ]);
    }

    /**
     * Get comments on tasks in a specific category.
     *
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
     */
    public function getCategoryTaskComments(Request $request, Category $category): JsonResponse
    {
        // Ensure the user can access this category
        $this->authorize('view', $category);

        $comments = $this->analyticsService->getCategoryTaskComments($category);

        return response()->json([
            'success' => true,
            'data' => $comments,
            'message' => 'Category task comments retrieved successfully',
        ]);
    }

    /**
     * Get tags used in tasks within a specific category.
     *
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
     */
    public function getCategoryTaskTags(Request $request, Category $category): JsonResponse
    {
        // Ensure the user can access this category
        $this->authorize('view', $category);

        $tags = $this->analyticsService->getCategoryTaskTags($category);

        return response()->json([
            'success' => true,
            'data' => $tags,
            'message' => 'Category task tags retrieved successfully',
        ]);
    }
} 