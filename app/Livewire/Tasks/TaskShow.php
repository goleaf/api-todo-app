<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TaskShow extends Component
{
    public $taskId;
    public $task;
    
    public function mount($id)
    {
        $this->taskId = $id;
        $this->loadTask();
    }
    
    public function loadTask()
    {
        $this->task = Task::where('id', $this->taskId)
            ->where('user_id', Auth::id())
            ->with(['user', 'category'])
            ->first();
    }
    
    public function toggleStatus()
    {
        if ($this->task && $this->task->user_id === Auth::id()) {
            $this->task->completed = !$this->task->completed;
            $this->task->completed_at = $this->task->completed ? now() : null;
            $this->task->save();
            
            $this->loadTask();
            
            $status = $this->task->completed ? 'completed' : 'marked as active';
            session()->flash('message', "Task {$status} successfully!");
        }
    }
    
    public function deleteTask()
    {
        if ($this->task && $this->task->user_id === Auth::id()) {
            $this->task->delete();
            
            session()->flash('message', 'Task deleted successfully!');
            
            return redirect()->route('tasks.index');
        }
    }
    
    public function render()
    {
        return view('livewire.tasks.task-show')
            ->layout('layouts.app', ['title' => $this->task ? 'Task: ' . $this->task->title : 'Task Not Found']);
    }
}
