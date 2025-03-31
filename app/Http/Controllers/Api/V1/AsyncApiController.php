<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Async\AsyncBatchTagOperationRequest;
use App\Http\Requests\Api\Async\AsyncBulkProcessTasksRequest;
use App\Models\Task;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Process batch tag operations asynchronously across multiple tasks.
     * 
     * This method allows adding or removing tags from multiple tasks at once,
     * which is especially useful for bulk operations on large datasets.
     */
    public function batchTagOperation(AsyncBatchTagOperationRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $taskIds = $validated['task_ids'];
        $tagNames = $validated['tags'];
        $operation = $validated['operation'];
        
        $userId = Auth::id();
        
        // Process the tag names to get their IDs
        $tagIds = [];
        foreach ($tagNames as $name) {
            if (empty(trim($name))) continue;
            
            // Find or create the tag
            $tag = Tag::findOrCreateForUser($name, $userId);
            $tagIds[] = $tag->id;
        }
        
        // Skip if no valid tags
        if (empty($tagIds)) {
            return $this->errorResponse(__('validation.tag.none_valid'), 422);
        }
        
        // Process each task
        $processed = [];
        foreach ($taskIds as $taskId) {
            try {
                $task = Task::forUser($userId)->find($taskId);
                
                if (!$task) {
                    $processed[] = [
                        'id' => $taskId,
                        'success' => false,
                        'message' => __('validation.task.not_found'),
                    ];
                    continue;
                }
                
                // Perform the operation
                if ($operation === 'add') {
                    $task->addTags($tagIds);
                    $message = __('messages.task.tags_added');
                } else {
                    $task->removeTags($tagIds);
                    $message = __('messages.task.tags_removed');
                }
                
                $processed[] = [
                    'id' => $taskId,
                    'success' => true,
                    'message' => $message,
                ];
                
            } catch (\Exception $e) {
                $processed[] = [
                    'id' => $taskId,
                    'success' => false,
                    'message' => __('messages.task.process_failed') . ': ' . $e->getMessage(),
                ];
            }
        }
        
        // Update tag usage counts
        $this->updateTagUsageCounts($userId);
        
        return $this->successResponse([
            'processed' => $processed,
            'total' => count($taskIds),
            'success_count' => count(array_filter($processed, fn ($item) => $item['success'])),
            'operation' => $operation,
            'tags' => $tagNames,
        ]);
    }
    
    /**
     * Update usage counts for all tags belonging to a user.
     */
    private function updateTagUsageCounts(int $userId): void
    {
        // Get all user tags
        $tags = Tag::forUser($userId)->get();
        
        foreach ($tags as $tag) {
            // Count tasks with this tag
            $count = $tag->tasks()->count();
            
            // Update usage count if different
            if ($tag->usage_count !== $count) {
                $tag->usage_count = $count;
                $tag->save();
            }
        }
    }
}
