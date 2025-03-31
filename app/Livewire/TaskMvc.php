<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;

class TaskMvc extends Component
{
    public $tasks = [];
    public $newTask = '';
    public $editingTaskId = null;
    public $editTaskText = '';
    public $filter = 'all';
    
    protected $rules = [
        'newTask' => 'required|min:3|max:255',
        'editTaskText' => 'required|min:3|max:255',
    ];

    public function mount()
    {
        $this->refreshTasks();
    }

    public function refreshTasks()
    {
        $query = Task::where('user_id', auth()->id());
        
        if ($this->filter === 'active') {
            $query->where('completed', false);
        } elseif ($this->filter === 'completed') {
            $query->where('completed', true);
        }
        
        $this->tasks = $query->orderBy('created_at', 'desc')->get();
    }

    public function addTask()
    {
        $this->validate([
            'newTask' => 'required|min:3|max:255',
        ]);

        Task::create([
            'title' => $this->newTask,
            'user_id' => auth()->id(),
            'completed' => false,
        ]);

        $this->newTask = '';
        $this->refreshTasks();
    }

    public function toggleComplete($taskId)
    {
        $task = Task::find($taskId);
        if ($task && $task->user_id === auth()->id()) {
            $task->completed = !$task->completed;
            $task->save();
            $this->refreshTasks();
        }
    }

    public function editTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task && $task->user_id === auth()->id()) {
            $this->editingTaskId = $taskId;
            $this->editTaskText = $task->title;
        }
    }

    public function updateTask()
    {
        $this->validate([
            'editTaskText' => 'required|min:3|max:255',
        ]);

        $task = Task::find($this->editingTaskId);
        if ($task && $task->user_id === auth()->id()) {
            $task->title = $this->editTaskText;
            $task->save();
            $this->editingTaskId = null;
            $this->editTaskText = '';
            $this->refreshTasks();
        }
    }

    public function cancelEdit()
    {
        $this->editingTaskId = null;
        $this->editTaskText = '';
    }

    public function deleteTask($taskId)
    {
        $task = Task::find($taskId);
        if ($task && $task->user_id === auth()->id()) {
            $task->delete();
            $this->refreshTasks();
        }
    }

    public function clearCompleted()
    {
        Task::where('user_id', auth()->id())
            ->where('completed', true)
            ->delete();
            
        $this->refreshTasks();
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->refreshTasks();
    }

    public function render()
    {
        return view('livewire.taskmvc');
    }
} 