<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimeEntryRequest;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeEntryController extends Controller
{
    /**
     * Display a listing of time entries.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $timeEntries = TimeEntry::where('user_id', Auth::id())
            ->with(['task'])
            ->latest()
            ->paginate(10);

        return view('frontend.time-entries.index', compact('timeEntries'));
    }

    /**
     * Show the form for creating a new time entry.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $tasks = Task::where('user_id', Auth::id())
            ->where('completed', false)
            ->orderBy('title')
            ->get();

        return view('frontend.time-entries.create', compact('tasks'));
    }

    /**
     * Store a newly created time entry.
     *
     * @param  \App\Http\Requests\TimeEntryRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TimeEntryRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        TimeEntry::create($validated);

        return redirect()->route('time-entries.index')
            ->with('success', 'Time entry created successfully.');
    }

    /**
     * Display the specified time entry.
     *
     * @param  \App\Models\TimeEntry  $timeEntry
     * @return \Illuminate\View\View
     */
    public function show(TimeEntry $timeEntry)
    {
        $this->authorize('view', $timeEntry);
        return view('frontend.time-entries.show', compact('timeEntry'));
    }

    /**
     * Show the form for editing the specified time entry.
     *
     * @param  \App\Models\TimeEntry  $timeEntry
     * @return \Illuminate\View\View
     */
    public function edit(TimeEntry $timeEntry)
    {
        $this->authorize('update', $timeEntry);
        
        $tasks = Task::where('user_id', Auth::id())
            ->where('completed', false)
            ->orderBy('title')
            ->get();

        return view('frontend.time-entries.edit', compact('timeEntry', 'tasks'));
    }

    /**
     * Update the specified time entry.
     *
     * @param  \App\Http\Requests\TimeEntryRequest  $request
     * @param  \App\Models\TimeEntry  $timeEntry
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TimeEntryRequest $request, TimeEntry $timeEntry)
    {
        $this->authorize('update', $timeEntry);
        
        $timeEntry->update($request->validated());

        return redirect()->route('time-entries.index')
            ->with('success', 'Time entry updated successfully.');
    }

    /**
     * Remove the specified time entry.
     *
     * @param  \App\Models\TimeEntry  $timeEntry
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(TimeEntry $timeEntry)
    {
        $this->authorize('delete', $timeEntry);
        
        $timeEntry->delete();

        return redirect()->route('time-entries.index')
            ->with('success', 'Time entry deleted successfully.');
    }

    /**
     * Start tracking time for a task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function start(Task $task)
    {
        $this->authorize('update', $task);
        
        if ($task->isTracking()) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'Time tracking is already started for this task.');
        }

        TimeEntry::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'started_at' => now(),
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Time tracking started successfully.');
    }

    /**
     * Stop tracking time for a task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stop(Task $task)
    {
        $this->authorize('update', $task);
        
        $timeEntry = $task->currentTimeEntry;

        if (!$timeEntry) {
            return redirect()->route('tasks.show', $task)
                ->with('error', 'No active time tracking found for this task.');
        }

        $timeEntry->update([
            'stopped_at' => now(),
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Time tracking stopped successfully.');
    }
} 