<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * Get overview statistics.
     */
    public function overview()
    {
        $user = Auth::user();
        
        $totalTasks = $user->tasks()->count();
        $completedTasks = $user->tasks()->completed()->count();
        $pendingTasks = $user->tasks()->incomplete()->count();
        $totalCategories = $user->categories()->count();
        
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        
        return response()->json([
            'data' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'completion_rate' => $completionRate,
                'total_categories' => $totalCategories
            ]
        ]);
    }

    /**
     * Get completion rate over time.
     */
    public function completionRate()
    {
        $user = Auth::user();
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();
        
        // Get total tasks created before or on each day
        $totalTasksByDay = $user->tasks()
            ->where('created_at', '<=', $endDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get()
            ->keyBy('date');
        
        // Get completed tasks completed on or before each day
        $completedTasksByDay = $user->tasks()
            ->where('completed', true)
            ->where('completed_at', '<=', $endDate)
            ->select(DB::raw('DATE(completed_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->get()
            ->keyBy('date');
        
        $result = [];
        $cumulativeTotal = 0;
        $cumulativeCompleted = 0;
        
        // Generate stats for the last 7 days
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            
            // Add new tasks for this day
            if (isset($totalTasksByDay[$dateString])) {
                $cumulativeTotal += $totalTasksByDay[$dateString]->count;
            }
            
            // Add completed tasks for this day
            if (isset($completedTasksByDay[$dateString])) {
                $cumulativeCompleted += $completedTasksByDay[$dateString]->count;
            }
            
            $completionRate = $cumulativeTotal > 0 ? round(($cumulativeCompleted / $cumulativeTotal) * 100) : 0;
            
            $result[] = [
                'date' => $dateString,
                'completion_rate' => $completionRate
            ];
        }
        
        return response()->json(['data' => $result]);
    }

    /**
     * Get task counts by category.
     */
    public function byCategory()
    {
        $user = Auth::user();
        
        $categories = $user->categories()
            ->leftJoin('tasks', function ($join) use ($user) {
                $join->on('categories.id', '=', 'tasks.category_id')
                    ->where('tasks.user_id', '=', $user->id);
            })
            ->select('categories.id as category_id', 'categories.name as category_name', 'categories.color as category_color')
            ->selectRaw('COUNT(tasks.id) as task_count')
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->get();
        
        return response()->json(['data' => $categories]);
    }

    /**
     * Get task counts by priority.
     */
    public function byPriority()
    {
        $user = Auth::user();
        
        $priorities = [
            ['priority' => 1, 'priority_label' => 'Low'],
            ['priority' => 2, 'priority_label' => 'Medium'],
            ['priority' => 3, 'priority_label' => 'High']
        ];
        
        $counts = $user->tasks()
            ->select('priority', DB::raw('count(*) as task_count'))
            ->groupBy('priority')
            ->get()
            ->keyBy('priority');
        
        $result = [];
        
        foreach ($priorities as $priority) {
            $priorityValue = $priority['priority'];
            $count = isset($counts[$priorityValue]) ? $counts[$priorityValue]->task_count : 0;
            
            $result[] = [
                'priority' => $priorityValue,
                'priority_label' => $priority['priority_label'],
                'task_count' => $count
            ];
        }
        
        return response()->json(['data' => $result]);
    }

    /**
     * Get task counts by date.
     */
    public function byDate()
    {
        $user = Auth::user();
        
        $tasksByDate = $user->tasks()
            ->select(DB::raw('DATE(due_date) as date'), DB::raw('count(*) as task_count'))
            ->whereNotNull('due_date')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return response()->json(['data' => $tasksByDate]);
    }

    /**
     * Get task completion time statistics.
     */
    public function completionTime()
    {
        $user = Auth::user();
        
        $completedTasks = $user->tasks()
            ->completed()
            ->whereNotNull('completed_at')
            ->get();
        
        $totalCompletedTasks = $completedTasks->count();
        
        if ($totalCompletedTasks === 0) {
            return response()->json([
                'data' => [
                    'average_days' => 0,
                    'fastest_completion' => 0,
                    'slowest_completion' => 0,
                    'total_completed_tasks' => 0
                ]
            ]);
        }
        
        $completionTimes = $completedTasks->map(function ($task) {
            $created = Carbon::parse($task->created_at);
            $completed = Carbon::parse($task->completed_at);
            return $created->diffInDays($completed);
        });
        
        $averageDays = round($completionTimes->avg());
        $fastestCompletion = $completionTimes->min();
        $slowestCompletion = $completionTimes->max();
        
        return response()->json([
            'data' => [
                'average_days' => $averageDays,
                'fastest_completion' => $fastestCompletion,
                'slowest_completion' => $slowestCompletion,
                'total_completed_tasks' => $totalCompletedTasks
            ]
        ]);
    }
} 