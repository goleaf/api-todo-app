<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Async\AsyncBulkProcessTasksRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AsyncApiController extends ApiController
{
    /**
     * Get dashboard statistics asynchronously.
     */
    public function getDashboardStats(): JsonResponse
    {
        // Simulate asynchronous data fetching
        $stats = [
            'tasks_count' => rand(10, 100),
            'completed_tasks' => rand(5, 50),
            'overdue_tasks' => rand(0, 10),
            'categories_count' => rand(3, 10),
            'users_count' => rand(1, 20),
        ];

        return $this->successResponse($stats);
    }

    /**
     * Fetch data from external APIs asynchronously.
     */
    public function fetchExternalApis(): JsonResponse
    {
        // Simulate fetching from external APIs
        $results = [
            'weather' => [
                'temp' => rand(0, 35),
                'condition' => ['Sunny', 'Cloudy', 'Rainy'][rand(0, 2)],
            ],
            'news' => [
                'items' => rand(3, 10),
                'source' => ['BBC', 'CNN', 'Reuters'][rand(0, 2)],
            ],
            'stock' => [
                'value' => rand(100, 500) / 10,
                'change' => (rand(-100, 100) / 10).'%',
            ],
        ];

        return $this->successResponse($results);
    }

    /**
     * Process tasks in bulk asynchronously.
     */
    public function bulkProcessTasks(AsyncBulkProcessTasksRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $taskIds = $validated['task_ids'];
        $action = $validated['action'];

        // Simulate processing tasks
        $processed = array_map(function ($id) use ($action) {
            return [
                'id' => $id,
                'success' => (bool) rand(0, 1),
                'action' => $action,
                'message' => rand(0, 1) ? 'Successfully processed' : 'Error processing task',
            ];
        }, $taskIds);

        return $this->successResponse([
            'processed' => $processed,
            'total' => count($taskIds),
            'success_count' => count(array_filter($processed, fn ($item) => $item['success'])),
        ]);
    }
}
