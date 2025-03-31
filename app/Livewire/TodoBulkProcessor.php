<?php

namespace App\Livewire;

use App\Models\Task;
use App\Services\HypervelService;
use Hypervel\Facades\Hypervel;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TaskBulkProcessor extends Component
{
    public bool $isProcessing = false;
    public string $processingStatus = '';
    public int $processedCount = 0;
    public int $totalToProcess = 0;
    public ?string $errorMessage = null;
    public array $results = [];
    public string $operationType = 'none';
    
    public array $selectedIds = [];
    public bool $allSelected = false;
    public array $operations = [
        'none' => 'Select Operation',
        'mark_completed' => 'Mark as Completed',
        'set_high_priority' => 'Set High Priority',
        'set_due_tomorrow' => 'Set Due Tomorrow',
        'categorize' => 'Categorize',
        'delete' => 'Delete Selected'
    ];
    
    public string $selectedCategory = '';
    public array $categories = [
        'work' => 'Work',
        'personal' => 'Personal',
        'errands' => 'Errands',
        'health' => 'Health',
        'education' => 'Education',
        'finance' => 'Finance'
    ];

    #[Computed]
    public function tasks(): Collection
    {
        return Task::where('user_id', auth()->id())
            ->latest()
            ->limit(50)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.task-processor');
    }

    public function toggleSelection($todoId): void
    {
        if (in_array($todoId, $this->selectedIds)) {
            $this->selectedIds = array_diff($this->selectedIds, [$todoId]);
        } else {
            $this->selectedIds[] = $todoId;
        }
        
        $this->allSelected = count($this->selectedIds) === $this->tasks->count();
    }

    public function selectAll(): void
    {
        $this->selectedIds = $this->tasks->pluck('id')->toArray();
        $this->allSelected = true;
    }

    public function deselectAll(): void
    {
        $this->selectedIds = [];
        $this->allSelected = false;
    }

    public function processBulkOperation(): void
    {
        if (empty($this->selectedIds)) {
            $this->errorMessage = 'Please select at least one task to process';
            return;
        }

        if ($this->operationType === 'none') {
            $this->errorMessage = 'Please select an operation';
            return;
        }
        
        if ($this->operationType === 'categorize' && empty($this->selectedCategory)) {
            $this->errorMessage = 'Please select a category';
            return;
        }

        $this->isProcessing = true;
        $this->processedCount = 0;
        $this->totalToProcess = count($this->selectedIds);
        $this->processingStatus = 'Starting...';
        $this->errorMessage = null;
        $this->results = [];

        try {
            // Get the selected todos
            $selectedTodos = Task::whereIn('id', $this->selectedIds)->get();
            
            // Perform async operation using Hypervel
            $hypervelService = app(HypervelService::class);
            
            $hypervelService->runAsync(function() use ($selectedTodos) {
                $this->processingStatus = 'Processing...';
                
                // Process todos in batches of 10
                $todoCount = $selectedTodos->count();
                $batchSize = 10;
                $batches = ceil($todoCount / $batchSize);
                
                for ($i = 0; $i < $batches; $i++) {
                    $batch = $selectedTodos->slice($i * $batchSize, $batchSize);
                    
                    // Process this batch concurrently
                    $processors = [];
                    foreach ($batch as $todo) {
                        $processors[] = function() use ($todo) {
                            $result = $this->processSingleTodo($todo);
                            $this->processedCount++;
                            return $result;
                        };
                    }
                    
                    // Run this batch with concurrency
                    $batchResults = Hypervel::concurrent($processors);
                    $this->results = array_merge($this->results, $batchResults);
                    $this->processingStatus = "Processed {$this->processedCount} of {$this->totalToProcess}";
                    
                    // Yield back to the event loop to update UI
                    Hypervel::yield();
                }
                
                $this->processingStatus = 'Completed';
                $this->isProcessing = false;
                
                // Refresh the todos list
                $this->dispatch('todos-updated');
            });
        } catch (\Exception $e) {
            $this->isProcessing = false;
            $this->processingStatus = 'Error';
            $this->errorMessage = "An error occurred: {$e->getMessage()}";
            report($e);
        }
    }

    private function processSingleTodo(Task $todo): array
    {
        try {
            $result = ['id' => $todo->id, 'title' => $todo->title, 'success' => true];
            
            switch ($this->operationType) {
                case 'mark_completed':
                    $todo->completed = true;
                    $todo->completed_at = now();
                    $todo->save();
                    $result['message'] = 'Marked as completed';
                    break;
                    
                case 'set_high_priority':
                    $todo->priority = 'high';
                    $todo->save();
                    $result['message'] = 'Set to high priority';
                    break;
                    
                case 'set_due_tomorrow':
                    $todo->due_date = now()->addDay();
                    $todo->save();
                    $result['message'] = 'Due date set to tomorrow';
                    break;
                    
                case 'categorize':
                    $todo->category = $this->selectedCategory;
                    $todo->save();
                    $result['message'] = "Categorized as {$this->categories[$this->selectedCategory]}";
                    break;
                    
                case 'delete':
                    $result['message'] = 'Deleted';
                    $todo->delete();
                    break;
                    
                default:
                    $result['success'] = false;
                    $result['message'] = 'Unknown operation';
            }
            
            // Simulate some processing time in non-test environments
            if (!app()->environment('testing')) {
                Hypervel::sleep(0.1);
            }
            
            return $result;
        } catch (\Exception $e) {
            return [
                'id' => $todo->id, 
                'title' => $todo->title, 
                'success' => false, 
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Mark all selected tasks as completed
     */
    public function bulkComplete(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }
        
        Task::whereIn('id', $this->selectedIds)->update([
            'completed' => true,
            'completed_at' => now(),
        ]);
        
        $this->dispatch('tasksUpdated');
    }
    
    /**
     * Mark all selected tasks as incomplete
     */
    public function bulkIncomplete(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }
        
        Task::whereIn('id', $this->selectedIds)->update([
            'completed' => false,
            'completed_at' => null,
        ]);
        
        $this->dispatch('tasksUpdated');
    }
    
    /**
     * Delete all selected tasks
     */
    public function bulkDelete(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }
        
        Task::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->allSelected = false;
        
        $this->dispatch('tasksUpdated');
    }
    
    /**
     * Set due date for all selected tasks
     */
    public function bulkSetDueDate(): void
    {
        if (empty($this->selectedIds) || empty($this->bulkDueDate)) {
            return;
        }
        
        Task::whereIn('id', $this->selectedIds)->update([
            'due_date' => $this->bulkDueDate,
        ]);
        
        $this->dispatch('tasksUpdated');
    }
    
    /**
     * Select all completed tasks
     */
    public function selectCompleted(): void
    {
        $this->selectedIds = $this->tasks->where('completed', true)->pluck('id')->toArray();
        $this->dispatch('selectionChanged');
    }
    
    /**
     * Select all incomplete tasks
     */
    public function selectIncomplete(): void
    {
        $this->selectedIds = $this->tasks->where('completed', false)->pluck('id')->toArray();
        $this->dispatch('selectionChanged');
    }
    
    /**
     * Select all tasks due today
     */
    public function selectDueToday(): void
    {
        $this->selectedIds = $this->tasks
            ->filter(function ($task) {
                return $task->due_date && $task->due_date->isToday();
            })
            ->pluck('id')
            ->toArray();
            
        $this->dispatch('selectionChanged');
    }
    
    /**
     * Select all overdue tasks
     */
    public function selectOverdue(): void
    {
        $this->selectedIds = $this->tasks
            ->filter(function ($task) {
                return $task->due_date && $task->due_date->isPast() && !$task->due_date->isToday();
            })
            ->pluck('id')
            ->toArray();
            
        $this->dispatch('selectionChanged');
    }
    
    /**
     * Select all upcoming tasks
     */
    public function selectUpcoming(): void
    {
        $this->selectedIds = $this->tasks
            ->filter(function ($task) {
                return $task->due_date && $task->due_date->isFuture() && !$task->due_date->isToday();
            })
            ->pluck('id')
            ->toArray();
            
        $this->dispatch('selectionChanged');
    }
} 