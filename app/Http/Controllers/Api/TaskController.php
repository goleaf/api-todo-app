<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Task\StoreTaskRequest;
use App\Http\Requests\Api\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskCollection;
use App\Models\Task;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Task::where('user_id', Auth::id())
            ->with(['category', 'tags']);
        
        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by completion status
        if ($request->has('completed')) {
            $query->where('completed', $request->boolean('completed'));
        }
        
        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Filter by due date
        if ($request->has('due_date')) {
            if ($request->due_date === 'today') {
                $query->whereDate('due_date', today());
            } elseif ($request->due_date === 'tomorrow') {
                $query->whereDate('due_date', today()->addDay());
            } elseif ($request->due_date === 'this_week') {
                $query->whereBetween('due_date', [today()->startOfWeek(), today()->endOfWeek()]);
            } elseif ($request->due_date === 'overdue') {
                $query->where('due_date', '<', today())->where('completed', false);
            } elseif ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('due_date', [$request->start_date, $request->end_date]);
            }
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Order
        $orderBy = $request->order_by ?? 'created_at';
        $orderDirection = $request->direction ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);
        
        // Pagination
        $perPage = $request->per_page ?? 15;
        $tasks = $query->paginate($perPage);
        
        return $this->successResponse(
            new TaskCollection($tasks)
        );
    }

    /**
     * Store a newly created task in storage.
     *
     * @param  \App\Http\Requests\Api\Task\StoreTaskRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTaskRequest $request)
    {
        $task = new Task($request->validated());
        $task->user_id = Auth::id();
        $task->save();
        
        // Attach tags if provided
        if ($request->has('tags')) {
            $task->tags()->attach($request->tags);
        }
        
        $task->load(['category', 'tags']);
        
        return $this->successResponse(
            new TaskResource($task),
            'Task created successfully',
            201
        );
    }

    /**
     * Display the specified task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $task = Task::where('user_id', Auth::id())->find($id);
        
        if (!$task) {
            return $this->notFoundResponse('Task not found');
        }
        
        $task->load(['category', 'tags', 'timeEntries', 'attachments']);
        
        return $this->successResponse(
            new TaskResource($task)
        );
    }

    /**
     * Update the specified task in storage.
     *
     * @param  \App\Http\Requests\Api\Task\UpdateTaskRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTaskRequest $request, $id)
    {
        $task = Task::where('user_id', Auth::id())->find($id);
        
        if (!$task) {
            return $this->notFoundResponse('Task not found');
        }
        
        $task->update($request->validated());
        
        // Sync tags if provided
        if ($request->has('tags')) {
            $task->tags()->sync($request->tags);
        }
        
        $task->load(['category', 'tags']);
        
        return $this->successResponse(
            new TaskResource($task),
            'Task updated successfully'
        );
    }

    /**
     * Toggle the completion status of the task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStatus($id)
    {
        $task = Task::where('user_id', Auth::id())->find($id);
        
        if (!$task) {
            return $this->notFoundResponse('Task not found');
        }
        
        $task->completed = !$task->completed;
        $task->completed_at = $task->completed ? now() : null;
        $task->save();
        
        return $this->successResponse(
            new TaskResource($task),
            'Task status updated successfully'
        );
    }

    /**
     * Remove the specified task from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $task = Task::where('user_id', Auth::id())->find($id);
        
        if (!$task) {
            return $this->notFoundResponse('Task not found');
        }
        
        $task->delete();
        
        return $this->successResponse(
            null,
            'Task deleted successfully'
        );
    }
}
