<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Category;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public $taskStats = [];
    public $categories = [];
    public $completionRate = 0;
    public $recentTasks = [];

    public function mount()
    {
        $this->loadTaskStats();
        $this->loadCategories();
        $this->loadRecentTasks();
        $this->calculateCompletionRate();
    }

    public function loadTaskStats()
    {
        $userId = auth()->id();
        
        $this->taskStats = [
            'total' => Task::where('user_id', $userId)->count(),
            'completed' => Task::where('user_id', $userId)->where('completed', true)->count(),
            'pending' => Task::where('user_id', $userId)->where('completed', false)->count(),
            'overdue' => Task::where('user_id', $userId)
                ->where('completed', false)
                ->where('due_date', '<', Carbon::today())
                ->count(),
        ];
    }

    public function loadCategories()
    {
        $userId = auth()->id();
        
        $categories = Category::where('user_id', $userId)
            ->withCount('tasks')
            ->orderBy('task_count', 'desc')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'color' => $category->color,
                    'task_count' => $category->tasks_count,
                ];
            });
            
        $this->categories = $categories;
    }
    
    public function loadRecentTasks()
    {
        $this->recentTasks = Task::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();
    }

    public function calculateCompletionRate()
    {
        if ($this->taskStats['total'] > 0) {
            $this->completionRate = round(($this->taskStats['completed'] / $this->taskStats['total']) * 100);
        } else {
            $this->completionRate = 0;
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
