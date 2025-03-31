<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'tasks_count' => Task::count(),
            'categories_count' => Category::count(),
            'completed_tasks_count' => Task::where('completed', true)->count(),
            'incomplete_tasks_count' => Task::where('completed', false)->count(),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
        ]);
    }
} 