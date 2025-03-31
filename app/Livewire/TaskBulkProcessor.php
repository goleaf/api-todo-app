<?php

namespace App\Livewire;

use App\Models\Task;
use Livewire\Component;

class TaskBulkProcessor extends Component
{
    public $tasks = [];
    public $selectedTasks = [];
    public $bulkAction = '';
    public $newStatus = false;
    public $confirmingDelete = false;

    public function mount()
    {
        $this->refreshTasks();
    }

    public function refreshTasks()
    {
        $this->tasks = Task::where('user_id', auth()->id())->get();
    }

    public function toggleSelect($taskId)
    {
        if (in_array($taskId, $this->selectedTasks)) {
            $this->selectedTasks = array_diff($this->selectedTasks, [$taskId]);
        } else {
            $this->selectedTasks[] = $taskId;
        }
    }

    public function selectAll()
    {
        $this->selectedTasks = $this->tasks->pluck('id')->toArray();
    }

    public function deselectAll()
    {
        $this->selectedTasks = [];
    }

    public function confirmDelete()
    {
        if (empty($this->selectedTasks)) {
            session()->flash('error', 'No tasks selected.');
            return;
        }
        
        $this->confirmingDelete = true;
    }

    public function deleteSelected()
    {
        Task::whereIn('id', $this->selectedTasks)->delete();
        $this->selectedTasks = [];
        $this->confirmingDelete = false;
        $this->refreshTasks();
        session()->flash('message', 'Selected tasks have been deleted.');
    }

    public function updateStatus()
    {
        if (empty($this->selectedTasks)) {
            session()->flash('error', 'No tasks selected.');
            return;
        }

        Task::whereIn('id', $this->selectedTasks)->update(['completed' => $this->newStatus]);
        $this->refreshTasks();
        $statusText = $this->newStatus ? 'completed' : 'incomplete';
        session()->flash('message', 'Selected tasks have been marked as ' . $statusText . '.');
    }

    public function render()
    {
        return view('livewire.task-bulk-processor');
    }
} 