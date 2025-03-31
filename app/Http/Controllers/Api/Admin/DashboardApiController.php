<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    /**
     * Get chart data for admin dashboard.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getChartData(Request $request): JsonResponse
    {
        $period = $request->input('period', 'week');
        
        $data = [
            'tasksByStatus' => $this->getTasksByStatus(),
            'tasksByPriority' => $this->getTasksByPriority(),
            'tasksByDate' => $this->getTasksByDate($period),
            'mostActiveUsers' => $this->getMostActiveUsers(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Get tasks grouped by status.
     *
     * @return array
     */
    private function getTasksByStatus(): array
    {
        $now = Carbon::now();
        
        $completed = Task::where('completed', true)->count();
        $overdue = Task::where('completed', false)
            ->whereNotNull('due_date')
            ->where('due_date', '<', $now)
            ->count();
        $pending = Task::where('completed', false)
            ->where(function($query) use ($now) {
                $query->whereNull('due_date')
                    ->orWhere('due_date', '>=', $now);
            })
            ->count();
            
        return [
            'completed' => $completed,
            'pending' => $pending,
            'overdue' => $overdue
        ];
    }
    
    /**
     * Get tasks grouped by priority.
     *
     * @return array
     */
    private function getTasksByPriority(): array
    {
        return Task::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();
    }
    
    /**
     * Get tasks created over time based on the requested period.
     *
     * @param string $period
     * @return array
     */
    private function getTasksByDate(string $period): array
    {
        $now = Carbon::now();
        
        // Define date range based on period
        switch ($period) {
            case 'year':
                $start = $now->copy()->subYear()->startOfDay();
                $format = 'M';
                $interval = '1 month';
                break;
            case 'month':
                $start = $now->copy()->subMonth()->startOfDay();
                $format = 'd M';
                $interval = '1 day';
                break;
            case 'week':
            default:
                $start = $now->copy()->subWeek()->startOfDay();
                $format = 'D';
                $interval = '1 day';
                break;
        }
        
        // Generate date periods
        $dateRange = CarbonPeriod::create($start, $interval, $now);
        
        // Initialize data with zeros for all dates
        $formattedDates = [];
        $counts = [];
        
        foreach ($dateRange as $date) {
            $formattedDate = $date->format($format);
            if (!isset($formattedDates[$formattedDate])) {
                $formattedDates[$formattedDate] = $formattedDate;
                $counts[$formattedDate] = 0;
            }
        }
        
        // Query tasks created in the date range
        $tasks = Task::select(
                DB::raw("DATE_FORMAT(created_at, '" . ($period === 'year' ? '%Y-%m' : '%Y-%m-%d') . "') as date"),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $start)
            ->groupBy('date')
            ->get();
        
        // Map task counts to formatted dates
        foreach ($tasks as $task) {
            $taskDate = Carbon::parse($task->date);
            $formattedDate = $taskDate->format($format);
            if (isset($counts[$formattedDate])) {
                $counts[$formattedDate] = $task->count;
            }
        }
        
        return [
            'labels' => array_values($formattedDates),
            'data' => array_values($counts)
        ];
    }
    
    /**
     * Get most active users based on task count.
     *
     * @param int $limit
     * @return mixed
     */
    private function getMostActiveUsers(int $limit = 5)
    {
        return User::withCount('tasks')
            ->orderBy('tasks_count', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'email', 'tasks_count']);
    }
} 