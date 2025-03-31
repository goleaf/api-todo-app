<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TasksController extends RestController
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = \App\Rest\Resources\TaskResource::class;
    
    /**
     * Toggle a task's completion status
     *
     * @param array $parameters
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle($parameters = [])
    {
        $task = Task::findOrFail($parameters['id']);
        $wasCompleted = $task->completed;
        
        $task->completed = !$wasCompleted;
        if (!$wasCompleted) {
            $task->completed_at = now();
        } else {
            $task->completed_at = null;
        }
        
        $task->save();
        
        return response()->json([
            'success' => true,
            'message' => $task->completed ? 'Task marked as completed' : 'Task marked as incomplete',
            'data' => $task
        ]);
    }
    
    /**
     * Update tags for a task
     *
     * @param array $parameters
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTags($parameters = [])
    {
        $task = Task::findOrFail($parameters['id']);
        $tagIds = $parameters['tags'] ?? [];
        
        $task->tags()->sync($tagIds);
        
        return response()->json([
            'success' => true,
            'message' => 'Tags updated successfully',
            'data' => $task->load('tags')
        ]);
    }
    
    /**
     * Perform a bulk tag operation on a task
     *
     * @param array $parameters
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkTagOperation($parameters = [])
    {
        $task = Task::findOrFail($parameters['id']);
        $operation = $parameters['operation'] ?? 'add';
        $tagIds = $parameters['tag_ids'] ?? [];
        
        if ($operation === 'add') {
            $task->tags()->syncWithoutDetaching($tagIds);
            $message = 'Tags added successfully';
        } elseif ($operation === 'remove') {
            $task->tags()->detach($tagIds);
            $message = 'Tags removed successfully';
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid operation',
                'errors' => ['operation' => 'Operation must be "add" or "remove"']
            ], 422);
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $task->load('tags')
        ]);
    }
    
    /**
     * Get task statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        $userId = auth()->id();
        
        $stats = [
            'total' => Task::where('user_id', $userId)->count(),
            'completed' => Task::where('user_id', $userId)->where('completed', true)->count(),
            'incomplete' => Task::where('user_id', $userId)->where('completed', false)->count(),
            'due_today' => Task::where('user_id', $userId)->dueToday()->count(),
            'overdue' => Task::where('user_id', $userId)->overdue()->count(),
            'by_priority' => [
                'low' => Task::where('user_id', $userId)->where('priority', 1)->count(),
                'medium' => Task::where('user_id', $userId)->where('priority', 2)->count(),
                'high' => Task::where('user_id', $userId)->where('priority', 3)->count(),
                'urgent' => Task::where('user_id', $userId)->where('priority', 4)->count(),
            ],
            'by_month' => DB::table('tasks')
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
                ->where('user_id', $userId)
                ->whereYear('created_at', date('Y'))
                ->groupBy(DB::raw('MONTH(created_at)'))
                ->orderBy('month')
                ->get()
                ->pluck('count', 'month')
                ->toArray(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
