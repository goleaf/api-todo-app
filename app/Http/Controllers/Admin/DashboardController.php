<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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
    
    /**
     * Get chart data for dashboard visualizations.
     */
    public function getChartData(Request $request): JsonResponse
    {
        $period = $request->input('period', 'week');
        
        $data = [];
        
        // Tasks by status for pie chart
        $data['tasksByStatus'] = [
            'completed' => Task::where('completed', true)->count(),
            'pending' => Task::where('completed', false)
                ->where(function($query) {
                    $query->whereNull('due_date')
                        ->orWhere('due_date', '>=', Carbon::today());
                })->count(),
            'overdue' => Task::where('completed', false)
                ->whereNotNull('due_date')
                ->where('due_date', '<', Carbon::today())->count(),
        ];
        
        // Tasks by priority
        $data['tasksByPriority'] = Task::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority')
            ->toArray();
        
        // Tasks created over time
        if ($period === 'week') {
            $startDate = Carbon::now()->subDays(7);
            $groupFormat = 'Y-m-d';
            $labelFormat = 'D';
        } elseif ($period === 'month') {
            $startDate = Carbon::now()->subDays(30);
            $groupFormat = 'Y-m-d';
            $labelFormat = 'd M';
        } else { // year
            $startDate = Carbon::now()->subMonths(12);
            $groupFormat = 'Y-m';
            $labelFormat = 'M Y';
        }
        
        $tasksByDate = Task::where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as date, COUNT(*) as count")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();
        
        // Generate all dates in the period for continuous data
        $dates = [];
        $current = clone $startDate;
        $endDate = Carbon::now();
        
        while ($current <= $endDate) {
            $formattedDate = $current->format($groupFormat);
            $dates[$formattedDate] = [
                'label' => $current->format($labelFormat),
                'count' => $tasksByDate[$formattedDate] ?? 0
            ];
            
            if ($period === 'year') {
                $current->addMonth();
            } else {
                $current->addDay();
            }
        }
        
        $data['tasksByDate'] = [
            'labels' => array_column($dates, 'label'),
            'data' => array_column($dates, 'count'),
        ];
        
        // Most active users
        $data['mostActiveUsers'] = User::withCount('tasks')
            ->orderByDesc('tasks_count')
            ->limit(5)
            ->get(['id', 'name', 'tasks_count'])
            ->map(function($user) {
                return [
                    'name' => $user->name,
                    'tasks_count' => $user->tasks_count,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
} 