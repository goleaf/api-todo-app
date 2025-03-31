<?php

namespace App\Livewire\Tasks;

use App\Models\Category;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TaskList extends Component
{
    use WithPagination;
    
    // Search and filters
    public $search = '';
    
    public $statusFilter = '';
    
    public $categoryFilter = '';
    
    public $priorityFilter = '';
    
    public $dateFilter = '';
    
    public $sortField = 'due_date';
    
    public $sortDirection = 'asc';
    
    public $categories = [];
    
    public $taskIdToDelete = null;
    
    protected $listeners = [
        'taskCreated' => '$refresh',
        'taskUpdated' => '$refresh',
        'taskDeleted' => '$refresh',
    ];
    
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'dateFilter' => ['except' => ''],
        'sortField' => ['except' => 'due_date'],
        'sortDirection' => ['except' => 'asc'],
    ];
    
    public function mount()
    {
        $this->loadCategories();
    }
    
    public function loadCategories()
    {
        $this->categories = Category::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();
    }
    
    public function resetFilters()
    {
        $this->statusFilter = '';
        $this->categoryFilter = '';
        $this->priorityFilter = '';
        $this->dateFilter = '';
        $this->search = '';
        $this->sortField = 'due_date';
        $this->sortDirection = 'asc';
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    public function toggleComplete($taskId)
    {
        $task = Task::find($taskId);
        
        if ($task->user_id !== Auth::id()) {
            return;
        }
        
        if ($task->completed) {
            $task->markAsIncomplete();
        } else {
            $task->markAsComplete();
        }
        
        $this->emit('task-updated', $taskId);
    }
    
    public function confirmTaskDeletion($taskId)
    {
        $this->taskIdToDelete = $taskId;
    }
    
    public function deleteTask()
    {
        if (!$this->taskIdToDelete) {
            return;
        }
        
        $task = Task::find($this->taskIdToDelete);
        
        if ($task && $task->user_id === Auth::id()) {
            $task->delete();
            $this->emit('task-deleted', $this->taskIdToDelete);
        }
        
        $this->taskIdToDelete = null;
    }
    
    public function render()
    {
        $query = Task::where('user_id', Auth::id());
        
        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply status filter
        if ($this->statusFilter === 'completed') {
            $query->where('completed', true);
        } elseif ($this->statusFilter === 'incomplete') {
            $query->where('completed', false);
        }
        
        // Apply category filter
        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }
        
        // Apply priority filter
        if ($this->priorityFilter !== '') {
            $query->where('priority', $this->priorityFilter);
        }
        
        // Apply date filter
        if ($this->dateFilter === 'today') {
            $query->whereDate('due_date', Carbon::today());
        } elseif ($this->dateFilter === 'week') {
            $query->whereBetween('due_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($this->dateFilter === 'month') {
            $query->whereBetween('due_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        }
        
        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);
        
        $tasks = $query->paginate(10);
        
        return view('livewire.tasks.task-list', [
            'tasks' => $tasks,
        ])->layout('layouts.app', ['title' => 'Tasks']);
    }
}
