<?php

namespace App\Services\Api;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    use ApiResponse;

    /**
     * Get dashboard data for the authenticated user.
     */
    public function getDashboardData(): JsonResponse
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->getAdminDashboard();
        }

        return $this->getUserDashboard();
    }

    /**
     * Get dashboard data for regular users.
     */
    protected function getUserDashboard(): JsonResponse
    {
        $user = Auth::user();
        $userId = $user->id;

        // Task statistics
        $tasks = [
            'total' => $user->tasks()->count(),
            'completed' => $user->completedTasks()->count(),
            'incomplete' => $user->incompleteTasks()->count(),
            'due_today' => $user->tasksDueToday()->count(),
            'overdue' => $user->overdueTasks()->count(),
            'upcoming' => $user->upcomingTasks()->count(),
        ];

        // Calculate completion rate
        $tasks['completion_rate'] = $tasks['total'] > 0
            ? round(($tasks['completed'] / $tasks['total']) * 100, 1)
            : 0;

        // Recent tasks
        $recentTasks = $user->tasks()
            ->latest()
            ->limit(5)
            ->get();

        // Tasks by category
        $tasksByCategory = Category::forUser($userId)
            ->withCount(['tasks', 'tasks as completed_tasks_count' => function ($query) {
                $query->where('completed', true);
            }])
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'color' => $category->color,
                    'icon' => $category->icon,
                    'tasks_count' => $category->tasks_count,
                    'completed_tasks_count' => $category->completed_tasks_count,
                    'completion_percentage' => $category->tasks_count > 0
                        ? round(($category->completed_tasks_count / $category->tasks_count) * 100, 1)
                        : 0,
                ];
            });

        // Tasks by priority
        $tasksByPriority = [
            'low' => $user->tasks()->withPriority(1)->count(),
            'medium' => $user->tasks()->withPriority(2)->count(),
            'high' => $user->tasks()->withPriority(3)->count(),
            'urgent' => $user->tasks()->withPriority(4)->count(),
        ];

        // Recent activity
        $recentActivity = $user->tasks()
            ->latest('updated_at')
            ->limit(10)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'action' => $task->created_at->eq($task->updated_at) ? 'created' : 'updated',
                    'completed' => $task->completed,
                    'timestamp' => $task->updated_at->diffForHumans(),
                ];
            });

        return $this->successResponse([
            'user' => $user->only('id', 'name', 'email', 'photo_url'),
            'tasks' => $tasks,
            'recent_tasks' => $recentTasks,
            'tasks_by_category' => $tasksByCategory,
            'tasks_by_priority' => $tasksByPriority,
            'recent_activity' => $recentActivity,
        ]);
    }

    /**
     * Get dashboard data for admin users.
     */
    protected function getAdminDashboard(): JsonResponse
    {
        // System stats
        $totalUsers = User::count();
        $totalTasks = Task::count();
        $totalCategories = Category::count();

        // User stats
        $userStats = [
            'total' => $totalUsers,
            'active_last_30_days' => User::whereHas('tasks', function ($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })->count(),
            'admins' => User::where('role', 'admin')->count(),
            'users' => User::where('role', 'user')->count(),
        ];

        // Task stats
        $taskStats = [
            'total' => $totalTasks,
            'completed' => Task::where('completed', true)->count(),
            'incomplete' => Task::where('completed', false)->count(),
            'due_today' => Task::dueToday()->count(),
            'overdue' => Task::overdue()->count(),
            'created_last_30_days' => Task::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // New users over time
        $newUsersOverTime = $this->getUsersCreatedByPeriod();

        // New tasks over time
        $newTasksOverTime = $this->getTasksCreatedByPeriod();

        // Top users by task count
        $topUsersByTaskCount = User::withCount('tasks')
            ->orderBy('tasks_count', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'email', 'tasks_count']);

        return $this->successResponse([
            'user_stats' => $userStats,
            'task_stats' => $taskStats,
            'category_count' => $totalCategories,
            'new_users_over_time' => $newUsersOverTime,
            'new_tasks_over_time' => $newTasksOverTime,
            'top_users_by_task_count' => $topUsersByTaskCount,
        ]);
    }

    /**
     * Get users created by time period.
     */
    protected function getUsersCreatedByPeriod(): array
    {
        $periods = [];

        // Last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $count = User::whereDate('created_at', $date)->count();
            $periods['daily'][] = [
                'date' => $date,
                'count' => $count,
            ];
        }

        // Last 4 weeks
        for ($i = 3; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();
            $count = User::whereBetween('created_at', [$startDate, $endDate])->count();
            $periods['weekly'][] = [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'count' => $count,
            ];
        }

        // Last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $startDate = Carbon::now()->subMonths($i)->startOfMonth();
            $endDate = Carbon::now()->subMonths($i)->endOfMonth();
            $count = User::whereBetween('created_at', [$startDate, $endDate])->count();
            $periods['monthly'][] = [
                'month' => $startDate->format('M Y'),
                'count' => $count,
            ];
        }

        return $periods;
    }

    /**
     * Get tasks created by time period.
     */
    protected function getTasksCreatedByPeriod(): array
    {
        $periods = [];

        // Last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $count = Task::whereDate('created_at', $date)->count();
            $periods['daily'][] = [
                'date' => $date,
                'count' => $count,
            ];
        }

        // Last 4 weeks
        for ($i = 3; $i >= 0; $i--) {
            $startDate = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = Carbon::now()->subWeeks($i)->endOfWeek();
            $count = Task::whereBetween('created_at', [$startDate, $endDate])->count();
            $periods['weekly'][] = [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'count' => $count,
            ];
        }

        // Last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $startDate = Carbon::now()->subMonths($i)->startOfMonth();
            $endDate = Carbon::now()->subMonths($i)->endOfMonth();
            $count = Task::whereBetween('created_at', [$startDate, $endDate])->count();
            $periods['monthly'][] = [
                'month' => $startDate->format('M Y'),
                'count' => $count,
            ];
        }

        return $periods;
    }
}
