<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Category;
use App\Models\Tag;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Perform a global search across all entities.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string', 'min:2'],
            'type' => ['nullable', 'string', 'in:tasks,categories,tags,time_entries,all'],
            'status' => ['nullable', 'string', 'in:todo,in_progress,completed'],
            'priority' => ['nullable', 'integer', 'in:1,2,3'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = $request->query('query');
        $type = $request->query('type', 'all');
        $perPage = $request->query('per_page', 15);

        $results = [];

        // Search tasks
        if ($type === 'all' || $type === 'tasks') {
            $taskQuery = Task::where('user_id', auth()->id())
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->with(['category', 'tags']);

            // Apply status filter
            if ($request->has('status')) {
                $taskQuery->where('status', $request->status);
            }

            // Apply priority filter
            if ($request->has('priority')) {
                $taskQuery->where('priority', $request->priority);
            }

            // Apply date range filter
            if ($request->has('date_from')) {
                $taskQuery->whereDate('due_date', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $taskQuery->whereDate('due_date', '<=', $request->date_to);
            }

            $results['tasks'] = $taskQuery->paginate($perPage);
        }

        // Search categories
        if ($type === 'all' || $type === 'categories') {
            $categoryQuery = Category::where('user_id', auth()->id())
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                })
                ->withCount('tasks');

            $results['categories'] = $categoryQuery->paginate($perPage);
        }

        // Search tags
        if ($type === 'all' || $type === 'tags') {
            $tagQuery = Tag::where('user_id', auth()->id())
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%");
                })
                ->withCount('tasks');

            $results['tags'] = $tagQuery->paginate($perPage);
        }

        // Search time entries
        if ($type === 'all' || $type === 'time_entries') {
            $timeEntryQuery = TimeEntry::where('user_id', auth()->id())
                ->where(function ($q) use ($query) {
                    $q->where('description', 'like', "%{$query}%");
                })
                ->with('task');

            // Apply date range filter
            if ($request->has('date_from')) {
                $timeEntryQuery->whereDate('started_at', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $timeEntryQuery->whereDate('started_at', '<=', $request->date_to);
            }

            $results['time_entries'] = $timeEntryQuery->paginate($perPage);
        }

        // Format response
        $response = [];
        foreach ($results as $entityType => $pagination) {
            $response[$entityType] = [
                'data' => $pagination->items(),
                'meta' => [
                    'current_page' => $pagination->currentPage(),
                    'last_page' => $pagination->lastPage(),
                    'per_page' => $pagination->perPage(),
                    'total' => $pagination->total()
                ]
            ];
        }

        return response()->json($response);
    }

    /**
     * Get search suggestions based on user's recent and popular items.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestions(Request $request)
    {
        $request->validate([
            'query' => ['nullable', 'string'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $query = $request->query('query');
        $limit = $request->query('limit', 5);
        $userId = auth()->id();

        $suggestions = [];

        // Recent tasks
        $recentTasks = Task::where('user_id', $userId)
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get(['id', 'title', 'status', 'due_date']);

        // Popular categories
        $popularCategories = Category::where('user_id', $userId)
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->withCount('tasks')
            ->orderBy('tasks_count', 'desc')
            ->limit($limit)
            ->get(['id', 'name']);

        // Frequently used tags
        $frequentTags = Tag::where('user_id', $userId)
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->withCount('tasks')
            ->orderBy('tasks_count', 'desc')
            ->limit($limit)
            ->get(['id', 'name']);

        return response()->json([
            'recent_tasks' => $recentTasks,
            'popular_categories' => $popularCategories,
            'frequent_tags' => $frequentTags
        ]);
    }

    /**
     * Search tasks by due date ranges.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByDueDate(Request $request)
    {
        $userId = auth()->id();
        $now = now();

        $overdueTasks = Task::where('user_id', $userId)
            ->where('status', '!=', 'completed')
            ->where('due_date', '<', $now->format('Y-m-d'))
            ->with(['category', 'tags'])
            ->get();

        $todayTasks = Task::where('user_id', $userId)
            ->where('status', '!=', 'completed')
            ->whereDate('due_date', $now)
            ->with(['category', 'tags'])
            ->get();

        $tomorrowTasks = Task::where('user_id', $userId)
            ->where('status', '!=', 'completed')
            ->whereDate('due_date', $now->addDay())
            ->with(['category', 'tags'])
            ->get();

        $thisWeekTasks = Task::where('user_id', $userId)
            ->where('status', '!=', 'completed')
            ->whereBetween('due_date', [
                $now->startOfWeek()->format('Y-m-d'),
                $now->endOfWeek()->format('Y-m-d')
            ])
            ->with(['category', 'tags'])
            ->get();

        return response()->json([
            'overdue' => $overdueTasks,
            'today' => $todayTasks,
            'tomorrow' => $tomorrowTasks,
            'this_week' => $thisWeekTasks
        ]);
    }
} 