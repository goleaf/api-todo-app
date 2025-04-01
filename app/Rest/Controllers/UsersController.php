<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class UsersController extends RestController
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = \App\Rest\Resources\UserResource::class;
    
    /**
     * Get user statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        $userId = auth()->id();
        $user = User::findOrFail($userId);
        
        $stats = [
            'tasks' => [
                'total' => $user->tasks()->count(),
                'completed' => $user->tasks()->where('completed', true)->count(),
                'incomplete' => $user->tasks()->where('completed', false)->count(),
                'due_today' => $user->tasks()->dueToday()->count(),
                'overdue' => $user->tasks()->overdue()->count(),
                'created_this_month' => $user->tasks()
                    ->whereRaw("strftime('%m', created_at) = strftime('%m', 'now')")
                    ->whereRaw("strftime('%Y', created_at) = strftime('%Y', 'now')")
                    ->count(),
                'completed_this_month' => $user->tasks()
                    ->where('completed', true)
                    ->whereRaw("strftime('%m', completed_at) = strftime('%m', 'now')")
                    ->whereRaw("strftime('%Y', completed_at) = strftime('%Y', 'now')")
                    ->count(),
            ],
            'categories' => [
                'total' => $user->categories()->count(),
                'with_tasks' => $user->categories()->has('tasks')->count(),
                'empty' => $user->categories()->doesntHave('tasks')->count(),
                'most_tasks' => $user->categories()
                    ->withCount('tasks')
                    ->orderBy('tasks_count', 'desc')
                    ->first(),
                'most_completed' => $user->categories()
                    ->withCount('completedTasks')
                    ->orderBy('completed_tasks_count', 'desc')
                    ->first(),
            ],
            'tags' => [
                'total' => $user->tags()->count(),
                'most_used' => $user->tags()
                    ->orderBy('usage_count', 'desc')
                    ->first(),
                'created_this_month' => $user->tags()
                    ->whereRaw("strftime('%m', created_at) = strftime('%m', 'now')")
                    ->whereRaw("strftime('%Y', created_at) = strftime('%Y', 'now')")
                    ->count(),
            ],
            'activity' => [
                'tasks_by_month' => DB::table('tasks')
                    ->select(DB::raw('strftime("%m", created_at) as month'), DB::raw('COUNT(*) as count'))
                    ->where('user_id', $userId)
                    ->whereRaw("strftime('%Y', created_at) = strftime('%Y', 'now')")
                    ->groupBy(DB::raw('strftime("%m", created_at)'))
                    ->orderBy('month')
                    ->get()
                    ->pluck('count', 'month')
                    ->toArray(),
                    
                'completions_by_month' => DB::table('tasks')
                    ->select(DB::raw('strftime("%m", completed_at) as month'), DB::raw('COUNT(*) as count'))
                    ->where('user_id', $userId)
                    ->whereNotNull('completed_at')
                    ->whereRaw("strftime('%Y', completed_at) = strftime('%Y', 'now')")
                    ->groupBy(DB::raw('strftime("%m", completed_at)'))
                    ->orderBy('month')
                    ->get()
                    ->pluck('count', 'month')
                    ->toArray(),
            ],
            'task_data' => [
                'task_creation_by_month' => DB::table('tasks')
                    ->select(DB::raw('strftime("%m", created_at) as month'), DB::raw('COUNT(*) as count'))
                    ->where('user_id', $userId)
                    ->whereRaw("strftime('%Y', created_at) = strftime('%Y', 'now')")
                    ->groupBy(DB::raw('strftime("%m", created_at)'))
                    ->orderBy('month')
                    ->get()
                    ->pluck('count', 'month')
                    ->toArray(),
                'task_completion_by_month' => DB::table('tasks')
                    ->select(DB::raw('strftime("%m", completed_at) as month'), DB::raw('COUNT(*) as count'))
                    ->where('user_id', $userId)
                    ->whereNotNull('completed_at')
                    ->whereRaw("strftime('%Y', completed_at) = strftime('%Y', 'now')")
                    ->groupBy(DB::raw('strftime("%m", completed_at)'))
                    ->orderBy('month')
                    ->get()
                    ->pluck('count', 'month')
                    ->toArray(),
            ],
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
