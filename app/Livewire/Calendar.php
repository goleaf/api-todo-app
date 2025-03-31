<?php

namespace App\Livewire;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Calendar extends Component
{
    public $currentMonth;

    public $currentYear;

    public $selectedDate;

    public $tasks = [];

    public $daysInMonth = [];

    public $monthName;

    public $weeks = [];

    public $monthTasks = [];

    public $selectedDateTasks = [];

    public function mount()
    {
        $today = Carbon::today();
        $this->currentMonth = $today->month;
        $this->currentYear = $today->year;
        $this->selectedDate = $today;
        $this->monthName = $this->getCurrentMonthName();

        $this->generateCalendar();
        $this->loadTasks();
    }

    public function generateCalendar()
    {
        $this->weeks = [];
        $this->monthTasks = [];

        $startOfMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->endOfMonth();

        // Start from the beginning of the week containing the first day of the month
        $startDate = $startOfMonth->copy()->startOfWeek();
        // End at the end of the week containing the last day of the month
        $endDate = $endOfMonth->copy()->endOfWeek();

        $currentDate = $startDate->copy();
        $currentWeek = [];

        while ($currentDate <= $endDate) {
            if (count($currentWeek) === 7) {
                $this->weeks[] = $currentWeek;
                $currentWeek = [];
            }

            $isCurrentMonth = $currentDate->month === $this->currentMonth;

            $currentWeek[] = [
                'day' => $currentDate->day,
                'date' => $currentDate->format('Y-m-d'),
                'isToday' => $currentDate->isToday(),
                'isCurrentMonth' => $isCurrentMonth,
                'isWeekend' => $currentDate->isWeekend(),
            ];

            $currentDate->addDay();
        }

        // Add the final week if not empty
        if (! empty($currentWeek)) {
            $this->weeks[] = $currentWeek;
        }

        // Load tasks for the month
        $this->loadMonthTasks();
    }

    public function loadMonthTasks()
    {
        if (! Auth::check()) {
            return;
        }

        $startOfMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->endOfMonth();

        $tasks = Task::where('user_id', Auth::id())
            ->whereBetween('due_date', [$startOfMonth, $endOfMonth])
            ->orderBy('due_date')
            ->orderBy('completed')
            ->get();

        // Group tasks by date
        $this->monthTasks = [];
        foreach ($tasks as $task) {
            $date = $task->due_date->format('Y-m-d');
            if (! isset($this->monthTasks[$date])) {
                $this->monthTasks[$date] = [];
            }
            $this->monthTasks[$date][] = $task;
        }
    }

    public function selectDate($date)
    {
        if ($date) {
            $this->selectedDate = Carbon::parse($date);
            $this->loadSelectedDateTasks();
        }
    }

    public function loadSelectedDateTasks()
    {
        if (! Auth::check() || ! $this->selectedDate) {
            $this->selectedDateTasks = [];

            return;
        }

        $date = $this->selectedDate->format('Y-m-d');
        $this->selectedDateTasks = Task::where('user_id', Auth::id())
            ->whereDate('due_date', $date)
            ->orderBy('completed')
            ->orderBy('due_date')
            ->get();
    }

    public function previousMonth()
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->monthName = $this->getCurrentMonthName();

        $this->generateCalendar();
        $this->loadTasks();
    }

    public function nextMonth()
    {
        $date = Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->monthName = $this->getCurrentMonthName();

        $this->generateCalendar();
        $this->loadTasks();
    }

    public function currentMonth()
    {
        $today = Carbon::today();
        $this->currentMonth = $today->month;
        $this->currentYear = $today->year;
        $this->selectedDate = $today;
        $this->monthName = $this->getCurrentMonthName();

        $this->generateCalendar();
        $this->loadSelectedDateTasks();
    }

    public function toggleTaskStatus($taskId)
    {
        $task = Task::find($taskId);

        if ($task && $task->user_id === Auth::id()) {
            $task->completed = ! $task->completed;
            $task->completed_at = $task->completed ? Carbon::now() : null;
            $task->save();

            $this->loadSelectedDateTasks();
            $this->loadMonthTasks();
        }
    }

    public function getCurrentMonthName()
    {
        return Carbon::createFromDate($this->currentYear, $this->currentMonth, 1)->format('F Y');
    }

    public function loadTasks()
    {
        $this->loadSelectedDateTasks();
        $this->loadMonthTasks();
    }

    public function render()
    {
        return view('livewire.calendar')
            ->layout('layouts.app', ['title' => 'Calendar']);
    }
}
