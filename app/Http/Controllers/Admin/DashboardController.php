<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = $this->getStats();
        $recentUsers = $this->getRecentUsers();
        $activityStats = $this->getActivityStats();
        
        return view('pages.admin.dashboard', compact('stats', 'recentUsers', 'activityStats'));
    }
    
    /**
     * Get system statistics.
     *
     * @return array
     */
    protected function getStats()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::where('completed', true)->count(),
            'total_categories' => Category::count(),
            'total_tags' => Tag::count(),
        ];
    }
    
    /**
     * Get recently registered users.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getRecentUsers($limit = 5)
    {
        return User::latest()
            ->take($limit)
            ->get(['id', 'name', 'email', 'created_at', 'is_active']);
    }
    
    /**
     * Get activity statistics for the chart.
     *
     * @param int $days
     * @return array
     */
    protected function getActivityStats($days = 7)
    {
        $stats = [
            'labels' => [],
            'users' => [],
            'tasks' => [],
        ];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $stats['labels'][] = now()->subDays($i)->format('M d');
            
            // Get user registrations for date
            $userCount = User::whereDate('created_at', $date)->count();
            $stats['users'][] = $userCount;
            
            // Get tasks created for date
            $taskCount = Task::whereDate('created_at', $date)->count();
            $stats['tasks'][] = $taskCount;
        }
        
        return $stats;
    }
}
