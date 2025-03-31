<?php

namespace App\Livewire\Components;

use App\Models\Task;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Todo extends Component
{
    public $newTodo = '';
    public $editingId = null;
    public $editingTitle = '';
    public $filter = 'all';
    
    protected $rules = [
        'newTodo' => 'required|min:3|max:255',
    ];

    public function render()
    {
        $user = Auth::user();
        $query = Task::where('user_id', $user->id);

        // Apply filters
        if ($this->filter === 'active') {
            $query->where('completed', false);
        } elseif ($this->filter === 'completed') {
            $query->where('completed', true);
        }

        $todos = $query->orderBy('created_at', 'desc')->get();
        $todosCount = $todos->count();
        $activeTodosCount = $todos->where('completed', false)->count();
        $completedTodosCount = $todos->where('completed', true)->count();

        return view('livewire.components.todo', [
            'todos' => $todos,
            'todosCount' => $todosCount,
            'activeTodosCount' => $activeTodosCount,
            'completedTodosCount' => $completedTodosCount,
        ]);
    }

    public function addTodo()
    {
        $this->validate();

        Task::create([
            'title' => $this->newTodo,
            'completed' => false,
            'user_id' => Auth::id(),
        ]);

        $this->newTodo = '';
    }

    public function toggleComplete($id)
    {
        $todo = Task::findOrFail($id);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function startEditing($id)
    {
        $todo = Task::findOrFail($id);
        $this->editingId = $id;
        $this->editingTitle = $todo->title;
    }

    public function updateTodo()
    {
        $this->validate([
            'editingTitle' => 'required|min:3|max:255',
        ]);

        $todo = Task::findOrFail($this->editingId);
        $todo->title = $this->editingTitle;
        $todo->save();

        $this->editingId = null;
        $this->editingTitle = '';
    }

    public function cancelEditing()
    {
        $this->editingId = null;
        $this->editingTitle = '';
    }

    public function deleteTodo($id)
    {
        Task::destroy($id);
    }

    public function clearCompleted()
    {
        Task::where('user_id', Auth::id())
            ->where('completed', true)
            ->delete();
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function toggleAllTodos()
    {
        $todos = Task::where('user_id', Auth::id())->get();
        $allCompleted = $todos->every(fn($todo) => $todo->completed);

        // If all are completed, mark all as active, otherwise mark all as completed
        Task::where('user_id', Auth::id())
            ->update(['completed' => !$allCompleted]);
    }
} 