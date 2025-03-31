<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends AdminController
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        // Basic stats
        $stats = [
            'users_count' => User::count(),
            'tasks_count' => Task::count(),
            'categories_count' => Category::count(),
            'tags_count' => Tag::count(),
            'completed_tasks_count' => Task::where('completed', true)->count(),
            'incomplete_tasks_count' => Task::where('completed', false)->count(),
            'overdue_tasks_count' => Task::where('completed', false)
                ->whereNotNull('due_date')
                ->where('due_date', '<', Carbon::today())
                ->count(),
        ];

        // Tasks by priority
        $stats['tasks_by_priority'] = Task::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get()
            ->keyBy('priority')
            ->map(function ($item) {
                return $item->count;
            })
            ->toArray();

        // Tasks by category (top 5)
        $stats['tasks_by_category'] = Category::withCount('tasks')
            ->orderByDesc('tasks_count')
            ->limit(5)
            ->get();

        // Recent tasks (last 5)
        $stats['recent_tasks'] = Task::with(['user', 'category'])
            ->latest()
            ->limit(5)
            ->get();

        // Tasks due this week
        $stats['due_this_week'] = Task::where('completed', false)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [Carbon::now(), Carbon::now()->endOfWeek()])
            ->count();

        // Tasks created in the last 30 days
        $stats['tasks_last_30_days'] = Task::where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        // Active users (users with tasks)
        $stats['active_users'] = User::has('tasks')
            ->withCount('tasks')
            ->orderByDesc('tasks_count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
        ]);
    }
} 