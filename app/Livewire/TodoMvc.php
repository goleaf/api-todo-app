<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class TaskMvc extends Component
{
    public $newTodo = '';
    public $filter = 'all';
    public $editing = null;
    public $editedTodo = '';
    
    protected $listeners = ['taskSaved' => '$refresh'];
    
    public function mount($filter = null)
    {
        if ($filter) {
            $this->filter = $filter;
        }
    }
    
    public function render()
    {
        $tasks = $this->getTasks();
        
        return view('livewire.taskmvc', [
            'tasks' => $tasks,
            'remaining' => $this->getIncompleteCount(),
            'allComplete' => $this->getIncompleteCount() === 0 && $tasks->count() > 0,
            'anyComplete' => $this->getCompletedCount() > 0,
        ]);
    }
    
    public function getTasks()
    {
        $query = Task::where('user_id', Auth::id());
        
        if ($this->filter === 'active') {
            $query->where('completed', false);
        } elseif ($this->filter === 'completed') {
            $query->where('completed', true);
        } elseif ($this->filter === 'due-today') {
            $query->whereDate('due_date', today())->where('completed', false);
        } elseif ($this->filter === 'overdue') {
            $query->where('due_date', '<', today())->where('completed', false);
        } elseif ($this->filter === 'upcoming') {
            $query->where('due_date', '>', today())->where('completed', false);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    public function getIncompleteCount()
    {
        return Task::where('user_id', Auth::id())->where('completed', false)->count();
    }
    
    public function getCompletedCount()
    {
        return Task::where('user_id', Auth::id())->where('completed', true)->count();
    }
    
    public function addTodo()
    {
        $this->validate([
            'newTodo' => 'required|min:3',
        ]);
        
        Task::create([
            'title' => $this->newTodo,
            'user_id' => Auth::id(),
            'completed' => false,
        ]);
        
        $this->newTodo = '';
    }
    
    public function toggleComplete($id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('update', $task);
        
        $task->completed = !$task->completed;
        $task->save();
    }
    
    public function startEditing($id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('update', $task);
        
        $this->editing = $id;
        $this->editedTodo = $task->title;
    }
    
    public function updateEditing()
    {
        $this->validate([
            'editedTodo' => 'required|min:3',
        ]);
        
        $task = Task::findOrFail($this->editing);
        $this->authorize('update', $task);
        
        $task->title = $this->editedTodo;
        $task->save();
        
        $this->editing = null;
        $this->editedTodo = '';
    }
    
    public function cancelEditing()
    {
        $this->editing = null;
        $this->editedTodo = '';
    }
    
    public function deleteTodo($id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('delete', $task);
        
        $task->delete();
    }
    
    public function markAllComplete()
    {
        Task::where('user_id', Auth::id())->update(['completed' => true]);
    }
    
    public function clearCompleted()
    {
        Task::where('user_id', Auth::id())->where('completed', true)->delete();
    }
} 