<?php

namespace App\Services\Api;

use App\Models\Category;
use App\Models\Task;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class DashboardService
{
    use ApiResponse;

    protected TaskService $taskService;
    protected CategoryService $categoryService;

    /**
     * DashboardService constructor.
     */
    public function __construct(TaskService $taskService, CategoryService $categoryService)
    {
        $this->taskService = $taskService;
        $this->categoryService = $categoryService;
    }

    /**
     * Get dashboard data for the authenticated user.
     */
    public function getDashboardData(): JsonResponse
    {
        $userId = auth()->id();
        $currentDate = now()->format('Y-m-d');
        
        // Get task statistics
        $totalTasks = Task::where('user_id', $userId)->count();
        $completedTasks = Task::where('user_id', $userId)->where('completed', true)->count();
        $pendingTasks = Task::where('user_id', $userId)->where('completed', false)->count();
        $overdueTasks = Task::where('user_id', $userId)
            ->where('completed', false)
            ->where('due_date', '<', $currentDate)
            ->count();
        
        // Get today's tasks
        $todayTasks = Task::where('user_id', $userId)
            ->where(function($query) use ($currentDate) {
                $query->where('due_date', $currentDate)
                    ->orWhere(function($query) use ($currentDate) {
                        $query->where('completed', false)
                            ->where('due_date', '<', $currentDate);
                    });
            })
            ->with('category')
            ->orderBy('priority', 'desc')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();
        
        // Get upcoming tasks (next 7 days)
        $nextWeek = now()->addDays(7)->format('Y-m-d');
        $upcomingTasks = Task::where('user_id', $userId)
            ->where('completed', false)
            ->where('due_date', '>', $currentDate)
            ->where('due_date', '<=', $nextWeek)
            ->with('category')
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();
        
        // Get categories with task counts
        $categories = Category::where('user_id', $userId)
            ->withCount(['tasks', 'tasks as completed_tasks_count' => function ($query) {
                $query->where('completed', true);
            }])
            ->take(5)
            ->get();
        
        // Get task completion rate over time (last 7 days)
        $last7Days = now()->subDays(6)->format('Y-m-d');
        $completionStats = Task::where('user_id', $userId)
            ->where('completed_at', '>=', $last7Days)
            ->selectRaw('DATE(completed_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();
        
        // Get priority distribution
        $priorityStats = [
            'low' => Task::where('user_id', $userId)->where('priority', 0)->count(),
            'medium' => Task::where('user_id', $userId)->where('priority', 1)->count(),
            'high' => Task::where('user_id', $userId)->where('priority', 2)->count(),
        ];
        
        // Compile all data
        $dashboardData = [
            'stats' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'overdue_tasks' => $overdueTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
            ],
            'tasks' => [
                'today' => $todayTasks,
                'upcoming' => $upcomingTasks,
            ],
            'categories' => $categories,
            'charts' => [
                'completion_over_time' => $completionStats,
                'priority_distribution' => $priorityStats,
            ],
        ];
        
        return $this->successResponse($dashboardData);
    }
} 