<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Task;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use ApiResponse;

    /**
     * Get dashboard data for the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Get task statistics
        $totalTasks = Task::where('user_id', $user->id)->count();
        $completedTasks = Task::where('user_id', $user->id)->where('completed', true)->count();
        $pendingTasks = Task::where('user_id', $user->id)->where('completed', false)->count();
        $overdueTasks = Task::where('user_id', $user->id)
            ->where('completed', false)
            ->whereDate('due_date', '<', now())
            ->count();
        
        // Get categories with task counts
        $categories = Category::where('user_id', $user->id)
            ->withCount('tasks')
            ->orderBy('tasks_count', 'desc')
            ->take(5)
            ->get();
        
        // Get recent tasks
        $recentTasks = Task::with('category')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Get upcoming deadlines
        $upcomingDeadlines = Task::with('category')
            ->where('user_id', $user->id)
            ->where('completed', false)
            ->whereDate('due_date', '>=', now())
            ->orderBy('due_date')
            ->take(5)
            ->get();
        
        return $this->successResponse(
            data: [
                'stats' => [
                    'total' => $totalTasks,
                    'completed' => $completedTasks,
                    'pending' => $pendingTasks,
                    'overdue' => $overdueTasks,
                    'completion_rate' => $totalTasks > 0 
                        ? round(($completedTasks / $totalTasks) * 100) 
                        : 0
                ],
                'categories' => $categories,
                'recentTasks' => $recentTasks,
                'upcomingDeadlines' => $upcomingDeadlines
            ]
        );
    }
} 