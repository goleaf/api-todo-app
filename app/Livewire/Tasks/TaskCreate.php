<?php

namespace App\Livewire\Tasks;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TaskCreate extends Component
{
    public $title = '';
    public $description = '';
    public $due_date = null;
    public $category_id = '';
    public $priority = 2; // Default priority: Normal
    
    protected function rules()
    {
        return [
            'title' => 'required|min:3|max:255',
            'description' => 'nullable|max:1000',
            'due_date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'priority' => 'required|integer|min:1|max:5',
        ];
    }
    
    public function createTask()
    {
        $validated = $this->validate();
        
        $task = new Task();
        $task->title = $validated['title'];
        $task->description = $validated['description'];
        $task->due_date = $validated['due_date'];
        $task->category_id = $validated['category_id'] ?: null;
        $task->priority = $validated['priority'];
        $task->user_id = Auth::id();
        $task->completed = false;
        $task->save();
        
        session()->flash('message', 'Task created successfully!');
        
        return redirect()->route('tasks.index');
    }
    
    public function render()
    {
        $categories = Category::orderBy('name')->get();
        
        return view('livewire.tasks.task-create', [
            'categories' => $categories
        ])->layout('layouts.app', ['title' => 'Create New Task']);
    }
} 