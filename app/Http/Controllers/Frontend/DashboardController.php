<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get task statistics
        $taskStats = [
            'total' => Task::where('user_id', $user->id)->count(),
            'completed' => Task::where('user_id', $user->id)->where('completed', true)->count(),
            'pending' => Task::where('user_id', $user->id)->where('completed', false)->count(),
            'overdue' => Task::where('user_id', $user->id)
                ->where('completed', false)
                ->where('due_date', '<', now())
                ->count(),
        ];

        // Get category statistics
        $categoryStats = Category::where('user_id', $user->id)
            ->withCount(['tasks' => function ($query) {
                $query->where('completed', false);
            }])
            ->get();

        // Get recent tasks
        $recentTasks = Task::where('user_id', $user->id)
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        // Get upcoming tasks
        $upcomingTasks = Task::where('user_id', $user->id)
            ->where('completed', false)
            ->where('due_date', '>', now())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        return view('frontend.dashboard.dashboard', compact(
            'taskStats',
            'categoryStats',
            'recentTasks',
            'upcomingTasks'
        ));
    }
} 