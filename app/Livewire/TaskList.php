<?php

namespace App\Livewire;

use App\Models\Task;
use App\Services\HypervelService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskList extends Component
{
    public $newTodo = '';
    public $editingId = null;
    public $editingTitle = '';
    public $editingDueDate = null;
    public $editingReminderAt = null;
    public $editingPriority = 0;
    public $editingTags = [];
    public $filter = 'all';
    public $isSaving = false;
    public $showDueDate = true;
    public $tagFilter = null;
    public $priorityFilter = null;
    public $reminderFilter = null;
    public $showCalendarView = false;
    public $calendarMonth = null;
    public $calendarYear = null;
    public $selectedDate = null;
    
    protected $rules = [
        'newTodo' => 'required|string|min:3|max:255',
        'editingTitle' => 'required|string|min:3|max:255',
        'editingDueDate' => 'nullable|date',
        'editingReminderAt' => 'nullable|date',
        'editingPriority' => 'integer|min:0|max:2',
        'editingTags' => 'array',
    ];
    
    protected $listeners = [
        'handleFilterChanged' => 'handleFilterChanged',
        'todoAdded' => '$refresh',
        'refreshTodos' => '$refresh',
        'dateSelected' => 'handleDateSelected',
        'reorderTodos' => 'reorderTodos',
    ];
    
    public function mount($filter = null)
    {
        if ($filter && in_array($filter, ['all', 'active', 'completed', 'due-today', 'overdue', 'upcoming', 'with-reminders', 'high-priority'])) {
            $this->filter = $filter;
        }
        
        $this->calendarMonth = now()->month;
        $this->calendarYear = now()->year;
    }

    public function render()
    {
        $query = Auth::check() 
            ? Task::where('user_id', Auth::id())
            : Task::where('session_id', session()->getId());
        
        // Apply priority filter
        if ($this->priorityFilter !== null) {
            $query->where('priority', $this->priorityFilter);
        }
        
        // Apply tag filter
        if ($this->tagFilter) {
            $query->withTag($this->tagFilter);
        }
        
        // Apply reminder filter
        if ($this->reminderFilter === 'with-reminders') {
            $query->whereNotNull('reminder_at');
        } elseif ($this->reminderFilter === 'without-reminders') {
            $query->whereNull('reminder_at');
        }
        
        // Apply filters
        if ($this->filter === 'active') {
            $query->where('completed', false);
        } elseif ($this->filter === 'completed') {
            $query->where('completed', true);
        } elseif ($this->filter === 'due-today') {
            $query->where('completed', false)
                  ->whereDate('due_date', Carbon::today());
        } elseif ($this->filter === 'overdue') {
            $query->where('completed', false)
                  ->where('due_date', '<', Carbon::today());
        } elseif ($this->filter === 'upcoming') {
            $query->where('completed', false)
                  ->where('due_date', '>', Carbon::today());
        } elseif ($this->filter === 'with-reminders') {
            $query->withReminders();
        } elseif ($this->filter === 'high-priority') {
            $query->where('priority', 2);
        }
        
        // Apply date filter for calendar view
        if ($this->selectedDate) {
            $query->whereDate('due_date', $this->selectedDate);
        }
        
        $todos = $query->orderBy('position', 'asc')
                       ->orderBy('priority', 'desc')
                       ->orderBy('created_at', 'desc')
                       ->get();
        
        $dueTodayCount = Auth::check() 
            ? Task::where('user_id', Auth::id())->where('completed', false)->whereDate('due_date', Carbon::today())->count()
            : Task::where('session_id', session()->getId())->where('completed', false)->whereDate('due_date', Carbon::today())->count();
            
        $overdueCount = Auth::check() 
            ? Task::where('user_id', Auth::id())->where('completed', false)->where('due_date', '<', Carbon::today())->count()
            : Task::where('session_id', session()->getId())->where('completed', false)->where('due_date', '<', Carbon::today())->count();
            
        $withRemindersCount = Auth::check()
            ? Task::where('user_id', Auth::id())->where('completed', false)->whereNotNull('reminder_at')->count()
            : Task::where('session_id', session()->getId())->where('completed', false)->whereNotNull('reminder_at')->count();
        
        $highPriorityCount = Auth::check()
            ? Task::where('user_id', Auth::id())->where('completed', false)->where('priority', 2)->count()
            : Task::where('session_id', session()->getId())->where('completed', false)->where('priority', 2)->count();
        
        $uniqueTags = $this->getUniqueTags();
            
        $calendarData = [];
        
        if ($this->showCalendarView) {
            $calendarData = $this->generateCalendarData();
        }
        
        return view('livewire.task-list', [
            'todos' => $todos,
            'todosCount' => $todos->count(),
            'activeTodosCount' => $todos->reject->completed->count(),
            'completedTodosCount' => $todos->where('completed', true)->count(),
            'dueTodayCount' => $dueTodayCount,
            'overdueCount' => $overdueCount,
            'withRemindersCount' => $withRemindersCount,
            'highPriorityCount' => $highPriorityCount,
            'uniqueTags' => $uniqueTags,
            'calendarData' => $calendarData,
        ]);
    }

    public function addTodo()
    {
        if ($this->isSaving) {
            return;
        }
        
        $this->isSaving = true;
        
        $this->validate([
            'newTodo' => 'required|string|min:3|max:255',
        ]);
        
        // Get highest position value
        $maxPosition = 0;
        $query = Auth::check() 
            ? Task::where('user_id', Auth::id())
            : Task::where('session_id', session()->getId());
            
        $maxTodo = $query->orderBy('position', 'desc')->first();
        if ($maxTodo) {
            $maxPosition = $maxTodo->position;
        }
        
        Task::create([
            'title' => $this->newTodo,
            'user_id' => Auth::check() ? Auth::id() : null,
            'session_id' => Auth::check() ? null : session()->getId(),
            'priority' => 0,
            'position' => $maxPosition + 1,
        ]);
        
        $this->newTodo = '';
        $this->isSaving = false;
        
        $this->dispatch('todoAdded');
    }

    public function toggleComplete($id)
    {
        $todo = Task::find($id);
        if ($todo) {
            $todo->completed = !$todo->completed;
            $todo->save();
        }
    }

    public function startEditing($id)
    {
        $todo = Task::find($id);
        if ($todo) {
            $this->editingId = $todo->id;
            $this->editingTitle = $todo->title;
            $this->editingDueDate = $todo->due_date;
            $this->editingReminderAt = $todo->reminder_at;
            $this->editingPriority = $todo->priority ?? 0;
            $this->editingTags = $todo->tags ?? [];
        }
    }

    public function updateTodo()
    {
        if ($this->isSaving) {
            return;
        }
        
        $this->isSaving = true;
        
        $this->validate([
            'editingTitle' => 'required|string|min:3|max:255',
            'editingDueDate' => 'nullable|date',
            'editingReminderAt' => 'nullable|date',
            'editingPriority' => 'integer|min:0|max:2',
        ]);
        
        $todo = Task::find($this->editingId);
        if ($todo) {
            $todo->title = $this->editingTitle;
            $todo->due_date = $this->editingDueDate;
            $todo->reminder_at = $this->editingReminderAt;
            $todo->priority = $this->editingPriority;
            $todo->tags = $this->editingTags;
            $todo->save();
        }
        
        $this->editingId = null;
        $this->editingTitle = '';
        $this->editingDueDate = null;
        $this->editingReminderAt = null;
        $this->editingPriority = 0;
        $this->editingTags = [];
        $this->isSaving = false;
    }

    public function cancelEditing()
    {
        $this->editingId = null;
        $this->editingTitle = '';
        $this->editingDueDate = null;
        $this->editingReminderAt = null;
        $this->editingPriority = 0;
        $this->editingTags = [];
    }

    public function deleteTodo($id)
    {
        $todo = Task::find($id);
        if ($todo) {
            $todo->delete();
        }
    }

    public function clearCompleted()
    {
        $query = Auth::check() 
            ? Task::where('user_id', Auth::id())
            : Task::where('session_id', session()->getId());
            
        // Use bulk deletion for better performance
        if (app()->bound(HypervelService::class)) {
            $hypervel = app(HypervelService::class);
            
            $completedTasks = $query->where('completed', true)->get();
            
            $hypervel->runBatch($completedTasks->all(), function($task) {
                return $task->delete();
            });
        } else {
            $query->where('completed', true)->delete();
        }
    }
    
    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->selectedDate = null;
    }
    
    public function handleFilterChanged($filter)
    {
        $this->filter = $filter;
    }
    
    public function toggleAllTodos()
    {
        $query = Auth::check() 
            ? Task::where('user_id', Auth::id())
            : Task::where('session_id', session()->getId());
        
        // Check if all are completed
        $allCompleted = $query->where('completed', false)->count() === 0;
        
        // Use bulk update for better performance
        if (app()->bound(HypervelService::class)) {
            $hypervel = app(HypervelService::class);
            
            $todos = $query->get();
            
            $hypervel->runBatch($todos->all(), function($todo) use ($allCompleted) {
                $todo->completed = !$allCompleted;
                return $todo->save();
            });
        } else {
            $query->update(['completed' => !$allCompleted]);
        }
    }
    
    public function toggleDueDateVisibility()
    {
        $this->showDueDate = !$this->showDueDate;
    }
    
    public function setDueDate($id, $date)
    {
        $todo = Task::find($id);
        if ($todo) {
            $todo->due_date = $date;
            $todo->save();
        }
    }
    
    public function batchSetDueDate($date)
    {
        $query = Auth::check() 
            ? Task::where('user_id', Auth::id())
            : Task::where('session_id', session()->getId());
        
        if ($this->filter === 'active') {
            $query->where('completed', false);
        } elseif ($this->filter === 'completed') {
            $query->where('completed', true);
        }
        
        // Use bulk update for better performance
        if (app()->bound(HypervelService::class)) {
            $hypervel = app(HypervelService::class);
            
            $todos = $query->get();
            
            $hypervel->runBatch($todos->all(), function($todo) use ($date) {
                $todo->due_date = $date;
                return $todo->save();
            });
        } else {
            $query->update(['due_date' => $date]);
        }
    }
    
    public function setReminder($id, $reminder)
    {
        $todo = Task::find($id);
        if ($todo) {
            $todo->reminder_at = $reminder;
            $todo->save();
        }
    }
    
    public function batchSetReminder($reminder)
    {
        $query = Auth::check() 
            ? Task::where('user_id', Auth::id())
            : Task::where('session_id', session()->getId());
        
        if ($this->filter === 'active') {
            $query->where('completed', false);
        } elseif ($this->filter === 'completed') {
            $query->where('completed', true);
        }
        
        // Use bulk update for better performance
        if (app()->bound(HypervelService::class)) {
            $hypervel = app(HypervelService::class);
            
            $todos = $query->get();
            
            $hypervel->runBatch($todos->all(), function($todo) use ($reminder) {
                $todo->reminder_at = $reminder;
                return $todo->save();
            });
        } else {
            $query->update(['reminder_at' => $reminder]);
        }
    }
    
    public function setPriority($id, $priority)
    {
        $todo = Task::find($id);
        if ($todo) {
            $todo->priority = $priority;
            $todo->save();
        }
    }
    
    public function addTag($id, $tag)
    {
        $todo = Task::find($id);
        if ($todo) {
            $tags = $todo->tags ?? [];
            if (!in_array($tag, $tags)) {
                $tags[] = $tag;
                $todo->tags = $tags;
                $todo->save();
            }
        }
    }
    
    public function removeTag($id, $tag)
    {
        $todo = Task::find($id);
        if ($todo) {
            $tags = $todo->tags ?? [];
            if (in_array($tag, $tags)) {
                $tags = array_values(array_filter($tags, function($t) use ($tag) {
                    return $t !== $tag;
                }));
                $todo->tags = $tags;
                $todo->save();
            }
        }
    }
    
    public function toggleCalendarView()
    {
        $this->showCalendarView = !$this->showCalendarView;
    }
    
    public function calendarNext()
    {
        if ($this->calendarMonth == 12) {
            $this->calendarMonth = 1;
            $this->calendarYear++;
        } else {
            $this->calendarMonth++;
        }
    }
    
    public function calendarPrevious()
    {
        if ($this->calendarMonth == 1) {
            $this->calendarMonth = 12;
            $this->calendarYear--;
        } else {
            $this->calendarMonth--;
        }
    }
    
    public function handleDateSelected($date)
    {
        $this->selectedDate = $date;
    }
    
    public function clearDateSelection()
    {
        $this->selectedDate = null;
    }
    
    protected function generateCalendarData()
    {
        $startOfMonth = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($this->calendarYear, $this->calendarMonth, 1)->endOfMonth();
        
        // Start from the beginning of the week containing the first day of the month
        $startDate = $startOfMonth->copy()->startOfWeek();
        // End at the end of the week containing the last day of the month
        $endDate = $endOfMonth->copy()->endOfWeek();
        
        $calendar = [];
        $currentDate = $startDate->copy();
        
        // Load task counts by date
        $query = Auth::check() 
            ? Task::where('user_id', Auth::id())
            : Task::where('session_id', session()->getId());
            
        $tasksByDate = $query
            ->whereBetween('due_date', [$startDate, $endDate])
            ->get()
            ->groupBy(function($task) {
                return $task->due_date->format('Y-m-d');
            });
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $isCurrentMonth = $currentDate->month === (int)$this->calendarMonth;
            
            $taskCount = isset($tasksByDate[$dateStr]) ? count($tasksByDate[$dateStr]) : 0;
            
            $calendar[] = [
                'date' => $dateStr,
                'day' => $currentDate->day,
                'isCurrentMonth' => $isCurrentMonth,
                'isToday' => $currentDate->isToday(),
                'isSelected' => $dateStr === $this->selectedDate,
                'hasTasks' => $taskCount > 0,
                'taskCount' => $taskCount,
            ];
            
            $currentDate->addDay();
        }
        
        return $calendar;
    }
    
    protected function getUniqueTags()
    {
        $query = Auth::check() 
            ? Task::where('user_id', Auth::id())
            : Task::where('session_id', session()->getId());
            
        $todos = $query->get();
        
        $tags = [];
        foreach ($todos as $todo) {
            if (!empty($todo->tags)) {
                foreach ($todo->tags as $tag) {
                    $tags[$tag] = ($tags[$tag] ?? 0) + 1;
                }
            }
        }
        
        return $tags;
    }
    
    public function setPriorityFilter($priority = null)
    {
        $this->priorityFilter = $priority;
    }
    
    public function setTagFilter($tag = null)
    {
        $this->tagFilter = $tag;
    }
    
    public function setReminderFilter($filter = null)
    {
        $this->reminderFilter = $filter;
    }
    
    // Reorder tasks based on drag and drop
    public function reorderTodos($todos)
    {
        foreach ($todos as $todoData) {
            $task = Task::find($todoData['id']);
            if ($task) {
                $task->position = $todoData['position'];
                $task->save();
            }
        }
    }
} 