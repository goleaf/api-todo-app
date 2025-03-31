<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\User;
use Livewire\Component;
use Hypervel\Facades\Hypervel;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class AsyncTodoList extends Component
{
    use WithPagination;

    public ?int $userId = null;
    public string $search = '';
    public string $filter = 'all';
    public int $perPage = 10;
    public bool $isLoading = false;

    public function mount(?int $userId = null): void
    {
        $this->userId = $userId ?? auth()->id();
    }

    public function render(): View
    {
        $this->isLoading = true;

        // Use Hypervel to load data asynchronously
        $data = Hypervel::concurrent([
            fn() => $this->loadTodos(),
            fn() => $this->loadStats(),
        ]);

        $todos = $data[0];
        $stats = $data[1];

        $this->isLoading = false;

        return view('livewire.async-todo-list', [
            'todos' => $todos,
            'stats' => $stats,
        ]);
    }

    private function loadTodos(): Collection
    {
        // This simulates a slow database query
        if (app()->environment('testing')) {
            // Don't actually sleep in tests
            $query = Task::query();
        } else {
            // Simulate delay in production without blocking
            Hypervel::sleep(0.5);
            $query = Task::query();
        }

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }

        if ($this->filter === 'completed') {
            $query->where('completed', true);
        } elseif ($this->filter === 'pending') {
            $query->where('completed', false);
        }

        return $query->latest()->paginate($this->perPage);
    }

    private function loadStats(): array
    {
        // This simulates multiple slow database queries
        if (!app()->environment('testing')) {
            Hypervel::sleep(0.3);
        }

        // Run multiple queries concurrently
        return Hypervel::concurrent([
            fn() => $this->getCompletedCount(),
            fn() => $this->getPendingCount(),
            fn() => $this->getOverdueCount(),
            fn() => $this->getPriorityCount(),
        ]);
    }

    private function getCompletedCount(): int
    {
        $query = Task::query()->where('completed', true);
        
        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }
        
        return $query->count();
    }

    private function getPendingCount(): int
    {
        $query = Task::query()->where('completed', false);
        
        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }
        
        return $query->count();
    }

    private function getOverdueCount(): int
    {
        $query = Task::query()
            ->where('completed', false)
            ->where('due_date', '<', now());
        
        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }
        
        return $query->count();
    }

    private function getPriorityCount(): int
    {
        $query = Task::query()
            ->where('priority', 'high');
        
        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }
        
        return $query->count();
    }

    public function toggleComplete(int $todoId): void
    {
        $this->isLoading = true;

        $todo = Task::find($todoId);
        
        if ($todo) {
            Hypervel::async(function() use ($todo) {
                $todo->completed = !$todo->completed;
                $todo->save();
                
                // Simulate API call or other operation
                if (!app()->environment('testing')) {
                    Hypervel::sleep(0.2);
                }
                
                if ($todo->completed) {
                    // Additional processing when marked complete
                    $this->processCompletedTodo($todo);
                }
            });
        }

        $this->isLoading = false;
    }

    private function processCompletedTodo(Task $todo): void
    {
        // This could be any additional processing logic
        if ($todo->has_subtasks) {
            // Also mark subtasks as completed
            $todo->subtasks()->update(['completed' => true]);
        }
    }

    public function markAllCompleted(): void
    {
        $this->isLoading = true;

        Hypervel::async(function() {
            $query = Task::query()->where('completed', false);
            
            if ($this->userId) {
                $query->where('user_id', $this->userId);
            }
            
            if ($this->search) {
                $query->where('title', 'like', "%{$this->search}%");
            }
            
            $todos = $query->get();
            
            // Process each todo concurrently
            foreach ($todos as $todo) {
                Hypervel::spawn(function() use ($todo) {
                    $todo->completed = true;
                    $todo->save();
                    $this->processCompletedTodo($todo);
                });
            }
        });

        $this->isLoading = false;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filter = 'all';
    }
} 