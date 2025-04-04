<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\TimeEntryRequest;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeEntryController extends BaseController
{
    /**
     * Display a listing of time entries.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $timeEntries = TimeEntry::where('user_id', Auth::id())
            ->with(['task'])
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
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        $timeEntry = TimeEntry::create($validated);

        return $this->successResponse($timeEntry->load('task'), 'Time entry created successfully');
    }

    /**
     * Display the specified time entry.
     *
     * @param  \App\Models\TimeEntry  $timeEntry
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(TimeEntry $timeEntry)
    {
        $this->authorize('view', $timeEntry);

        return $this->successResponse($timeEntry->load('task'));
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
        $this->authorize('update', $timeEntry);

        $timeEntry->update($request->validated());

        return $this->successResponse($timeEntry->load('task'), 'Time entry updated successfully');
    }

    /**
     * Remove the specified time entry.
     *
     * @param  \App\Models\TimeEntry  $timeEntry
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(TimeEntry $timeEntry)
    {
        $this->authorize('delete', $timeEntry);

        $timeEntry->delete();

        return $this->successResponse(null, 'Time entry deleted successfully');
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

        // Check if there's already an active time entry
        $activeEntry = TimeEntry::where('user_id', Auth::id())
            ->whereNull('end_time')
            ->first();

        if ($activeEntry) {
            return $this->errorResponse('You already have an active time entry');
        }

        $timeEntry = TimeEntry::create([
            'user_id' => Auth::id(),
            'task_id' => $task->id,
            'start_time' => now(),
        ]);

        return $this->successResponse($timeEntry->load('task'), 'Time entry started successfully');
    }

    /**
     * Stop the active time entry.
     *
     * @param  \App\Models\TimeEntry  $timeEntry
     * @return \Illuminate\Http\JsonResponse
     */
    public function stop(TimeEntry $timeEntry)
    {
        $this->authorize('update', $timeEntry);

        if ($timeEntry->end_time) {
            return $this->errorResponse('This time entry has already been stopped');
        }

        $timeEntry->update([
            'end_time' => now(),
        ]);

        return $this->successResponse($timeEntry->load('task'), 'Time entry stopped successfully');
    }
} 