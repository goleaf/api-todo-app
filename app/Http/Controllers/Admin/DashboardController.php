<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = [
            'total_tasks' => Task::where('user_id', auth()->id())->count(),
            'completed_tasks' => Task::where('user_id', auth()->id())->completed()->count(),
            'overdue_tasks' => Task::where('user_id', auth()->id())->overdue()->count(),
            'due_today' => Task::where('user_id', auth()->id())->dueToday()->count(),
            'categories' => Category::where('user_id', auth()->id())->count(),
        ];
        
        $recent_tasks = Task::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->with('category')
            ->get();
            
        return view('admin.dashboard', compact('stats', 'recent_tasks'));
    }
}
