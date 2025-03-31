<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $stats = [
        'total' => 0,
        'completed' => 0,
        'pending' => 0,
        'overdue' => 0,
    ];

    public $completionRate = 0;
    public $categoryStats = [];
    public $recentActivity = [];
    public $loading = true;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $user = Auth::user();

        if ($user) {
            $userId = $user->id;
            
            // Basic statistics
            $this->stats['total'] = Task::where('user_id', $userId)->count();
            $this->stats['completed'] = Task::where('user_id', $userId)->where('completed', true)->count();
            $this->stats['pending'] = Task::where('user_id', $userId)->where('completed', false)->count();
            $this->stats['overdue'] = Task::where('user_id', $userId)
                ->where('completed', false)
                ->whereNotNull('due_date')
                ->where('due_date', '<', now())
                ->count();

            // Calculate completion rate
            $this->completionRate = $this->stats['total'] > 0
                ? round(($this->stats['completed'] / $this->stats['total']) * 100)
                : 0;

            // Tasks per category
            $this->categoryStats = Category::select('categories.id', 'categories.name', 'categories.color')
                ->selectRaw('COUNT(tasks.id) as task_count')
                ->leftJoin('tasks', function ($join) use ($userId) {
                    $join->on('categories.id', '=', 'tasks.category_id')
                        ->where('tasks.user_id', $userId);
                })
                ->where('categories.user_id', $userId)
                ->groupBy('categories.id', 'categories.name', 'categories.color')
                ->orderByRaw('COUNT(tasks.id) DESC')
                ->get()
                ->toArray();

            // Weekly task creation/completion
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $weeklyActivity = Task::where('user_id', $userId)
                ->where(function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                        ->orWhereBetween('updated_at', [$startOfWeek, $endOfWeek]);
                })
                ->orderBy('updated_at', 'desc')
                ->get();

            // Format for recent activity
            foreach ($weeklyActivity as $task) {
                if ($task->created_at >= $startOfWeek && $task->created_at <= $endOfWeek) {
                    $this->recentActivity[] = [
                        'id' => $task->id,
                        'title' => $task->title,
                        'type' => 'created',
                        'date' => $task->created_at,
                    ];
                }

                if ($task->created_at != $task->updated_at &&
                    $task->updated_at >= $startOfWeek &&
                    $task->updated_at <= $endOfWeek) {
                    $this->recentActivity[] = [
                        'id' => $task->id,
                        'title' => $task->title,
                        'type' => $task->completed ? 'completed' : 'updated',
                        'date' => $task->updated_at,
                    ];
                }
            }

            // Sort by date
            usort($this->recentActivity, function ($a, $b) {
                return $b['date']->timestamp - $a['date']->timestamp;
            });

            // Limit to 10 most recent activities
            $this->recentActivity = array_slice($this->recentActivity, 0, 10);

            // Get recent tasks (last 5 modified)
            $recentTasks = Task::where('user_id', $userId)
                ->orderBy('updated_at', 'desc')
                ->take(5)
                ->get();
            
            // Get upcoming tasks (next 5 due)
            $upcomingTasks = Task::where('user_id', $userId)
                ->where('completed', false)
                ->whereNotNull('due_date')
                ->where('due_date', '>=', Carbon::today())
                ->orderBy('due_date', 'asc')
                ->take(5)
                ->get();
        }

        $this->loading = false;
    }

    public function toggleComplete($taskId)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($taskId);
        $task->completed = !$task->completed;
        
        if ($task->completed) {
            $task->completed_at = now();
        } else {
            $task->completed_at = null;
        }
        
        $task->save();
        
        $this->loadStats();
    }

    public function render()
    {
        $userId = Auth::id();
        
        // Get recent tasks (last 5 modified)
        $recentTasks = Task::where('user_id', $userId)
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
        
        // Get upcoming tasks (next 5 due)
        $upcomingTasks = Task::where('user_id', $userId)
            ->where('completed', false)
            ->whereNotNull('due_date')
            ->where('due_date', '>=', Carbon::today())
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();
        
        return view('livewire.dashboard', [
            'recentTasks' => $recentTasks,
            'upcomingTasks' => $upcomingTasks,
        ])->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
