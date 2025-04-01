<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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

        return view('user.time-entries.index', compact('timeEntries'));
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

        return view('user.time-entries.create', compact('tasks'));
    }

    /**
     * Store a newly created time entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after:started_at',
            'description' => 'nullable|string|max:1000',
        ]);

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
        return view('user.time-entries.show', compact('timeEntry'));
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

        return view('user.time-entries.edit', compact('timeEntry', 'tasks'));
    }

    /**
     * Update the specified time entry.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TimeEntry  $timeEntry
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, TimeEntry $timeEntry)
    {
        $this->authorize('update', $timeEntry);

        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after:started_at',
            'description' => 'nullable|string|max:1000',
        ]);

        $timeEntry->update($validated);

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
     * Start a new time entry for a task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function start(Task $task)
    {
        $this->authorize('update', $task);

        $timeEntry = TimeEntry::create([
            'user_id' => Auth::id(),
            'task_id' => $task->id,
            'started_at' => now(),
            'description' => 'Started tracking time',
        ]);

        return response()->json([
            'success' => true,
            'timeEntry' => $timeEntry,
        ]);
    }

    /**
     * Stop the current time entry for a task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function stop(Task $task)
    {
        $this->authorize('update', $task);

        $timeEntry = TimeEntry::where('user_id', Auth::id())
            ->where('task_id', $task->id)
            ->whereNull('ended_at')
            ->latest()
            ->first();

        if ($timeEntry) {
            $timeEntry->update([
                'ended_at' => now(),
                'description' => 'Stopped tracking time',
            ]);
        }

        return response()->json([
            'success' => true,
            'timeEntry' => $timeEntry,
        ]);
    }
} 