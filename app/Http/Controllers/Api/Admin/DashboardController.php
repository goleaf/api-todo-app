<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\Task;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Display the admin dashboard data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Get user statistics
        $userStats = [
            'total' => User::where('is_admin', false)->count(),
            'active' => User::where('is_admin', false)
                ->where('email_verified_at', '!=', null)
                ->count(),
            'inactive' => User::where('is_admin', false)
                ->where('email_verified_at', null)
                ->count(),
        ];

        // Get task statistics
        $taskStats = [
            'total' => Task::count(),
            'completed' => Task::where('completed', true)->count(),
            'pending' => Task::where('completed', false)->count(),
            'overdue' => Task::where('completed', false)
                ->where('due_date', '<', now())
                ->count(),
        ];

        // Get category statistics
        $categoryStats = Category::withCount(['tasks' => function ($query) {
            $query->where('completed', false);
        }])->get();

        // Get recent tasks
        $recentTasks = Task::with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        // Get recent users
        $recentUsers = User::where('is_admin', false)
            ->latest()
            ->take(5)
            ->get();

        return $this->successResponse([
            'userStats' => $userStats,
            'taskStats' => $taskStats,
            'categoryStats' => $categoryStats,
            'recentTasks' => $recentTasks,
            'recentUsers' => $recentUsers,
        ]);
    }
} 