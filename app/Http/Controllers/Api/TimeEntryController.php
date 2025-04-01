<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TimeEntry\StoreTimeEntryRequest;
use App\Http\Requests\Api\TimeEntry\UpdateTimeEntryRequest;
use App\Models\Task;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TimeEntryController extends Controller
{
    /**
     * Display a listing of time entries.
     */
    public function index(Request $request)
    {
        $query = TimeEntry::where('user_id', auth()->id())
            ->with('task');

        // Filter by task
        if ($request->has('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        // Filter by date range
        if ($request->has('start_date')) {
            $query->whereDate('started_at', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('started_at', '<=', $request->end_date);
        }

        // Sort options
        $sortField = $request->get('sort_by', 'started_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $allowedSortFields = ['started_at', 'duration', 'created_at'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
        }

        $timeEntries = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $timeEntries->items(),
            'meta' => [
                'current_page' => $timeEntries->currentPage(),
                'last_page' => $timeEntries->lastPage(),
                'per_page' => $timeEntries->perPage(),
                'total' => $timeEntries->total()
            ]
        ]);
    }

    /**
     * Store a newly created time entry.
     */
    public function store(StoreTimeEntryRequest $request)
    {
        // Verify task belongs to user
        $task = Task::findOrFail($request->task_id);
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized access to task'
            ], Response::HTTP_FORBIDDEN);
        }

        $timeEntry = new TimeEntry();
        $timeEntry->user_id = auth()->id();
        $timeEntry->task_id = $request->task_id;
        $timeEntry->started_at = Carbon::parse($request->started_at);
        $timeEntry->duration = $request->duration;
        $timeEntry->description = $request->description;
        $timeEntry->save();

        $timeEntry->load('task');

        return response()->json([
            'message' => 'Time entry created successfully',
            'data' => $timeEntry
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified time entry.
     */
    public function show(TimeEntry $timeEntry)
    {
        $this->authorize('view', $timeEntry);

        $timeEntry->load('task');

        return response()->json([
            'data' => $timeEntry
        ]);
    }

    /**
     * Update the specified time entry.
     */
    public function update(UpdateTimeEntryRequest $request, TimeEntry $timeEntry)
    {
        $this->authorize('update', $timeEntry);

        if ($request->has('started_at')) {
            $timeEntry->started_at = Carbon::parse($request->started_at);
        }
        if ($request->has('duration')) {
            $timeEntry->duration = $request->duration;
        }
        if ($request->has('description')) {
            $timeEntry->description = $request->description;
        }

        $timeEntry->save();
        $timeEntry->load('task');

        return response()->json([
            'message' => 'Time entry updated successfully',
            'data' => $timeEntry
        ]);
    }

    /**
     * Remove the specified time entry.
     */
    public function destroy(TimeEntry $timeEntry)
    {
        $this->authorize('delete', $timeEntry);

        $timeEntry->delete();

        return response()->json([
            'message' => 'Time entry deleted successfully'
        ]);
    }
    
    /**
     * Start a new time entry for a task.
     */
    public function start(Request $request, Task $task)
    {
        // Authorize the request
        $this->authorize('view', $task);
        
        // Check if there's already a running time entry for this user
        $runningEntry = $request->user()->timeEntries()
            ->whereNull('ended_at')
            ->first();
            
        if ($runningEntry) {
            // Stop the running entry
            $runningEntry->stop();
        }
        
        $validated = $request->validate([
            'description' => ['nullable', 'string'],
            'is_billable' => ['nullable', 'boolean'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
        ]);
        
        // Create the new time entry
        $timeEntry = $request->user()->timeEntries()->create([
            'task_id' => $task->id,
            'started_at' => Carbon::now(),
            'description' => $validated['description'] ?? null,
            'is_billable' => $validated['is_billable'] ?? false,
            'hourly_rate' => $validated['hourly_rate'] ?? null,
        ]);
        
        // Load task relationship
        $timeEntry->load('task');
        
        return response()->json([
            'success' => true,
            'message' => 'Time tracking started',
            'data' => $timeEntry,
        ], 201);
    }
    
    /**
     * Stop a running time entry.
     */
    public function stop(Request $request, TimeEntry $timeEntry)
    {
        // Authorize the request
        $this->authorize('update', $timeEntry);
        
        // Make sure the time entry is running
        if ($timeEntry->ended_at !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Time entry is not running',
            ], 400);
        }
        
        // Stop the time entry
        $timeEntry->stop();
        
        // Load task relationship
        $timeEntry->load('task');
        
        return response()->json([
            'success' => true,
            'message' => 'Time tracking stopped',
            'data' => $timeEntry,
        ]);
    }
}
