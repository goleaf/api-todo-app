<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Statistics\StatisticsRequest;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    /**
     * Get overall task statistics.
     *
     * @param StatisticsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function taskStats(StatisticsRequest $request)
    {
        $userId = auth()->id();
        $period = $request->get('period', 'all'); // all, week, month, year
        $now = now();

        $query = Task::where('user_id', $userId);

        // Apply period filter
        switch ($period) {
            case 'week':
                $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year);
                break;
            case 'year':
                $query->whereYear('created_at', $now->year);
                break;
        }

        $stats = [
            'total' => $query->count(),
            'completed' => $query->where('status', 'completed')->count(),
            'in_progress' => $query->where('status', 'in_progress')->count(),
            'todo' => $query->where('status', 'todo')->count(),
            'overdue' => $query->where('status', '!=', 'completed')
                ->where('due_date', '<', $now->format('Y-m-d'))
                ->count(),
            'completion_rate' => $query->count() > 0 
                ? round(($query->where('status', 'completed')->count() / $query->count()) * 100, 2)
                : 0,
            'priority_distribution' => [
                'high' => $query->where('priority', 3)->count(),
                'medium' => $query->where('priority', 2)->count(),
                'low' => $query->where('priority', 1)->count(),
            ],
            'category_distribution' => Category::where('user_id', $userId)
                ->withCount(['tasks' => function ($q) use ($period, $now) {
                    switch ($period) {
                        case 'week':
                            $q->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                            break;
                        case 'month':
                            $q->whereMonth('created_at', $now->month)
                                ->whereYear('created_at', $now->year);
                            break;
                        case 'year':
                            $q->whereYear('created_at', $now->year);
                            break;
                    }
                }])
                ->get(['id', 'name', 'tasks_count']),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Get time tracking statistics.
     *
     * @param StatisticsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function timeStats(StatisticsRequest $request)
    {
        $userId = auth()->id();
        $period = $request->get('period', 'all');
        $now = now();

        $query = TimeEntry::where('user_id', $userId);

        // Apply period filter
        switch ($period) {
            case 'week':
                $query->whereBetween('started_at', [$now->startOfWeek(), $now->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('started_at', $now->month)
                    ->whereYear('started_at', $now->year);
                break;
            case 'year':
                $query->whereYear('started_at', $now->year);
                break;
        }

        $stats = [
            'total_time' => $query->sum('duration'),
            'average_daily_time' => round($query->avg('duration'), 2),
            'most_tracked_day' => $query->select(
                DB::raw('DATE(started_at) as date'),
                DB::raw('SUM(duration) as total_duration')
            )
                ->groupBy('date')
                ->orderBy('total_duration', 'desc')
                ->first(),
            'category_time_distribution' => TimeEntry::where('user_id', $userId)
                ->join('tasks', 'time_entries.task_id', '=', 'tasks.id')
                ->join('categories', 'tasks.category_id', '=', 'categories.id')
                ->select(
                    'categories.name',
                    DB::raw('SUM(time_entries.duration) as total_duration')
                )
                ->groupBy('categories.id', 'categories.name')
                ->get(),
            'hourly_distribution' => TimeEntry::where('user_id', $userId)
                ->select(DB::raw('HOUR(started_at) as hour'), DB::raw('COUNT(*) as count'))
                ->groupBy('hour')
                ->orderBy('hour')
                ->get(),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Get productivity trends.
     *
     * @param StatisticsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productivityTrends(StatisticsRequest $request)
    {
        $userId = auth()->id();
        $days = $request->get('days', 30);
        $now = now();
        $startDate = $now->copy()->subDays($days);

        // Daily task completion trend
        $taskCompletionTrend = Task::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $now])
            ->select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('COUNT(*) as completed_count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Daily time tracking trend
        $timeTrackingTrend = TimeEntry::where('user_id', $userId)
            ->whereBetween('started_at', [$startDate, $now])
            ->select(
                DB::raw('DATE(started_at) as date'),
                DB::raw('SUM(duration) as total_duration')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Task completion time trend
        $completionTimeTrend = Task::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $now])
            ->select(
                DB::raw('DATE(updated_at) as date'),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_completion_time')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'data' => [
                'task_completion_trend' => $taskCompletionTrend,
                'time_tracking_trend' => $timeTrackingTrend,
                'completion_time_trend' => $completionTimeTrend
            ]
        ]);
    }

    /**
     * Get tag usage statistics.
     *
     * @param StatisticsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tagStats(StatisticsRequest $request)
    {
        $userId = auth()->id();
        $period = $request->get('period', 'all');
        $now = now();

        $query = Tag::where('user_id', $userId)
            ->withCount(['tasks' => function ($q) use ($period, $now) {
                switch ($period) {
                    case 'week':
                        $q->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                        break;
                    case 'month':
                        $q->whereMonth('created_at', $now->month)
                            ->whereYear('created_at', $now->year);
                        break;
                    case 'year':
                        $q->whereYear('created_at', $now->year);
                        break;
                }
            }]);

        $stats = [
            'most_used_tags' => $query->orderBy('tasks_count', 'desc')
                ->limit(10)
                ->get(['id', 'name', 'tasks_count']),
            'tag_completion_rates' => Tag::where('user_id', $userId)
                ->withCount([
                    'tasks',
                    'tasks as completed_tasks_count' => function ($q) {
                        $q->where('status', 'completed');
                    }
                ])
                ->having('tasks_count', '>', 0)
                ->get()
                ->map(function ($tag) {
                    $tag->completion_rate = $tag->tasks_count > 0
                        ? round(($tag->completed_tasks_count / $tag->tasks_count) * 100, 2)
                        : 0;
                    return $tag;
                }),
        ];

        return response()->json(['data' => $stats]);
    }
} 