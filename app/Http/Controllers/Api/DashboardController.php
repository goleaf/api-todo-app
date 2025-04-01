<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics and data.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $stats = [
            'tasks' => [
                'total' => Task::where('user_id', $user->id)->count(),
                'completed' => Task::where('user_id', $user->id)->completed()->count(),
                'incomplete' => Task::where('user_id', $user->id)->incomplete()->count(),
                'overdue' => Task::where('user_id', $user->id)->overdue()->count(),
                'due_today' => Task::where('user_id', $user->id)->dueToday()->count(),
                'due_this_week' => Task::where('user_id', $user->id)->dueThisWeek()->count(),
                'high_priority' => Task::where('user_id', $user->id)->highPriority()->count(),
            ],
            'categories' => [
                'total' => Category::where('user_id', $user->id)->count(),
                'with_tasks' => Category::where('user_id', $user->id)
                    ->whereHas('tasks')
                    ->count(),
            ],
        ];
        
        // Get recent tasks
        $recentTasks = Task::where('user_id', $user->id)
            ->with(['category', 'tags'])
            ->latest()
            ->take(5)
            ->get();
            
        // Get categories with task counts
        $categories = Category::where('user_id', $user->id)
            ->withCount(['tasks', 'tasks as completed_tasks_count' => function ($query) {
                $query->where('completed', true);
            }])
            ->get();
            
        return response()->json([
            'data' => [
                'stats' => $stats,
                'recent_tasks' => $recentTasks,
                'categories' => $categories,
            ]
        ]);
    }
}
