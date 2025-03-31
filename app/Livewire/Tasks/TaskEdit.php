<?php

namespace App\Livewire\Tasks;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TaskEdit extends Component
{
    public $taskId;
    public $title = '';
    public $description = '';
    public $due_date = null;
    public $category_id = '';
    public $priority = 2;
    public $completed = false;
    
    protected function rules()
    {
        return [
            'title' => 'required|min:3|max:255',
            'description' => 'nullable|max:1000',
            'due_date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'priority' => 'required|integer|min:1|max:5',
            'completed' => 'boolean',
        ];
    }
    
    public function mount($id)
    {
        $this->taskId = $id;
        $this->loadTask();
    }
    
    public function loadTask()
    {
        $task = Task::where('id', $this->taskId)
            ->where('user_id', Auth::id())
            ->first();
            
        if (!$task) {
            return redirect()->route('tasks.index')
                ->with('error', 'Task not found or access denied.');
        }
        
        $this->title = $task->title;
        $this->description = $task->description;
        $this->due_date = $task->due_date ? $task->due_date->format('Y-m-d') : null;
        $this->category_id = $task->category_id;
        $this->priority = $task->priority;
        $this->completed = $task->completed;
    }
    
    public function updateTask()
    {
        $validated = $this->validate();
        
        $task = Task::where('id', $this->taskId)
            ->where('user_id', Auth::id())
            ->first();
            
        if (!$task) {
            return redirect()->route('tasks.index')
                ->with('error', 'Task not found or access denied.');
        }
        
        $task->title = $validated['title'];
        $task->description = $validated['description'];
        $task->due_date = $validated['due_date'];
        $task->category_id = $validated['category_id'] ?: null;
        $task->priority = $validated['priority'];
        $task->completed = $validated['completed'];
        $task->completed_at = $validated['completed'] ? now() : null;
        $task->save();
        
        session()->flash('message', 'Task updated successfully!');
        
        return redirect()->route('tasks.show', $task->id);
    }
    
    public function render()
    {
        $categories = Category::orderBy('name')->get();
        
        return view('livewire.tasks.task-edit', [
            'categories' => $categories
        ])->layout('layouts.app', ['title' => 'Edit Task']);
    }
} 