<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\TimeEntryRequest;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\Request;

class TimeEntryController extends BaseController
{
    /**
     * Display a listing of time entries.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $timeEntries = TimeEntry::with(['user', 'task'])
            ->latest()
            ->paginate(10);

        return $this->successResponse($timeEntries);
    }

    /**
     * Store a newly created time entry.
     *
     * @param  \App\Http\Requests\TimeEntryRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TimeEntryRequest $request)
    {
        $timeEntry = TimeEntry::create($request->validated());

        return $this->successResponse($timeEntry->load(['user', 'task']), 'Time entry created successfully');
    }

    /**
     * Display the specified time entry.
     *
     * @param  \App\Models\TimeEntry  $timeEntry
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(TimeEntry $timeEntry)
    {
        return $this->successResponse($timeEntry->load(['user', 'task']));
    }

    /**
     * Update the specified time entry.
     *
     * @param  \App\Http\Requests\TimeEntryRequest  $request
     * @param  \App\Models\TimeEntry  $timeEntry
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TimeEntryRequest $request, TimeEntry $timeEntry)
    {
        $timeEntry->update($request->validated());

        return $this->successResponse($timeEntry->load(['user', 'task']), 'Time entry updated successfully');
    }

    /**
     * Remove the specified time entry.
     *
     * @param  \App\Models\TimeEntry  $timeEntry
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(TimeEntry $timeEntry)
    {
        $timeEntry->delete();

        return $this->successResponse(null, 'Time entry deleted successfully');
    }

    /**
     * Get time entries by user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function userTimeEntries(User $user)
    {
        $timeEntries = TimeEntry::where('user_id', $user->id)
            ->with('task')
            ->latest()
            ->paginate(10);

        return $this->successResponse($timeEntries);
    }

    /**
     * Get time entries by task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function taskTimeEntries(Task $task)
    {
        $timeEntries = TimeEntry::where('task_id', $task->id)
            ->with('user')
            ->latest()
            ->paginate(10);

        return $this->successResponse($timeEntries);
    }

    /**
     * Get time entries by date range.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dateRange(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $timeEntries = TimeEntry::with(['user', 'task'])
            ->whereBetween('start_time', [$validated['start_date'], $validated['end_date']])
            ->latest()
            ->paginate(10);

        return $this->successResponse($timeEntries);
    }
} 