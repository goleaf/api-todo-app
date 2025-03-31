<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Services\HypervelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\HypervelException;

class AsyncApiController extends Controller
{
    private HypervelService $hypervelService;

    public function __construct(HypervelService $hypervelService)
    {
        $this->hypervelService = $hypervelService;
    }

    /**
     * Get dashboard statistics for a user with concurrent database queries
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getDashboardStats(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        try {
            // Run multiple database queries concurrently
            $stats = $this->hypervelService->runConcurrently([
                'tasks_count' => fn() => Task::where('user_id', $userId)->count(),
                'completed_count' => fn() => Task::where('user_id', $userId)->where('completed', true)->count(),
                'pending_count' => fn() => Task::where('user_id', $userId)->where('completed', false)->count(),
                'overdue_count' => fn() => Task::where('user_id', $userId)
                    ->where('completed', false)
                    ->where('due_date', '<', now())
                    ->count(),
                'recent_tasks' => fn() => Task::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (HypervelException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch external APIs concurrently
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchExternalApis(Request $request): JsonResponse
    {
        try {
            // Fetch multiple external APIs concurrently
            $apiUrls = [
                'weather' => 'https://api.weather.gov/gridpoints/TOP/31,80/forecast',
                'github' => 'https://api.github.com/users/laravel',
                'quotes' => 'https://api.quotable.io/random',
            ];

            $headers = [
                'User-Agent' => 'Laravel Hypervel Example',
                'Accept' => 'application/json',
            ];

            $results = $this->hypervelService->fetchMultipleApis($apiUrls, $headers);

            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (HypervelException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch external APIs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk process and update tasks
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkProcessTasks(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $tasks = Task::where('user_id', $userId)->get();

            // Process tasks concurrently
            $results = $this->hypervelService->processCollection($tasks, function ($task) {
                // Simulate some complex processing
                $task->processed_at = now();
                $task->status = $this->determineStatus($task);
                $task->save();

                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'status' => $task->status,
                    'processed_at' => $task->processed_at->toIso8601String(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Tasks processed successfully',
                'data' => $results
            ]);
        } catch (HypervelException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Determine the status of a task based on business rules
     *
     * @param Task $task
     * @return string
     */
    private function determineStatus(Task $task): string
    {
        if ($task->completed) {
            return 'completed';
        }

        if ($task->due_date && $task->due_date->isPast()) {
            return 'overdue';
        }

        if ($task->due_date && $task->due_date->isToday()) {
            return 'due-today';
        }

        if ($task->priority === 2) { // High priority
            return 'high-priority';
        }

        return 'pending';
    }
} 