<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Todo extends Component
{
    use WithPagination;

    public $title;
    public $description;
    public $due_date;
    public $priority = 0; // Default to low priority
    public $category_id;
    public $filter = 'all';
    public $search = '';
    public $isEditing = false;
    public $editTaskId = null;
    public $showTaskModal = false;
    
    protected $rules = [
        'title' => 'required|min:3',
        'description' => 'nullable',
        'due_date' => 'nullable|date',
        'priority' => 'required|integer|min:0|max:2',
        'category_id' => 'nullable|exists:categories,id',
    ];

    public function mount()
    {
        $this->resetForm();
    }

    public function render()
    {
        $user = Auth::user();
        $query = Task::forUser($user->id);

        // Apply filters
        if ($this->filter === 'completed') {
            $query->completed();
        } elseif ($this->filter === 'incomplete') {
            $query->incomplete();
        } elseif ($this->filter === 'today') {
            $query->dueToday();
        } elseif ($this->filter === 'overdue') {
            $query->overdue();
        } elseif ($this->filter === 'this-week') {
            $query->dueThisWeek();
        } elseif (is_numeric($this->filter)) {
            // Filter by category if filter is a number
            $query->inCategory($this->filter);
        }

        // Apply search
        if (!empty($this->search)) {
            $query->search($this->search);
        }

        $tasks = $query->orderBy('due_date', 'asc')
                       ->orderBy('priority', 'desc')
                       ->paginate(10);

        $categories = Category::forUser($user->id)->get();
        
        return view('livewire.tasks.task', [
            'tasks' => $tasks,
            'categories' => $categories,
        ]);
    }

    public function createTask()
    {
        $this->validate();

        $task = new Task();
        $task->title = $this->title;
        $task->description = $this->description;
        $task->due_date = $this->due_date;
        $task->priority = $this->priority;
        $task->category_id = $this->category_id;
        $task->user_id = Auth::id();
        $task->save();

        $this->resetForm();
        $this->dispatch('task-created');
    }

    public function editTask($taskId)
    {
        $this->isEditing = true;
        $this->editTaskId = $taskId;
        $task = Task::find($taskId);
        
        $this->title = $task->title;
        $this->description = $task->description;
        $this->due_date = $task->due_date ? $task->due_date->format('Y-m-d') : null;
        $this->priority = $task->priority;
        $this->category_id = $task->category_id;
        
        $this->showTaskModal = true;
    }

    public function updateTask()
    {
        $this->validate();

        $task = Task::find($this->editTaskId);
        $task->title = $this->title;
        $task->description = $this->description;
        $task->due_date = $this->due_date;
        $task->priority = $this->priority;
        $task->category_id = $this->category_id;
        $task->save();

        $this->resetForm();
        $this->dispatch('task-updated');
    }

    public function toggleComplete($taskId)
    {
        $task = Task::find($taskId);
        
        if ($task->completed) {
            $task->markAsIncomplete();
        } else {
            $task->markAsComplete();
        }
        
        $this->dispatch('task-updated');
    }

    public function deleteTask($taskId)
    {
        Task::destroy($taskId);
        $this->dispatch('task-deleted');
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->due_date = '';
        $this->priority = 0;
        $this->category_id = null;
        $this->isEditing = false;
        $this->editTaskId = null;
        $this->showTaskModal = false;
    }

    public function showModal()
    {
        $this->resetForm();
        $this->showTaskModal = true;
    }

    public function closeModal()
    {
        $this->showTaskModal = false;
        $this->resetForm();
    }

    public function applyFilter($filter)
    {
        $this->filter = $filter;
    }
}
