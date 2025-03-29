<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Models\Task;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::with(['category'])
            ->where('user_id', Auth::id());
            
        // Filter by status
        if ($request->has('status')) {
            $status = $request->status;
            if ($status === 'completed') {
                $query->where('completed', true);
            } elseif ($status === 'pending') {
                $query->where('completed', false);
            } elseif ($status === 'overdue') {
                $query->where('completed', false)
                    ->whereDate('due_date', '<', now());
            }
        }
        
        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by due date
        if ($request->has('due') && $request->due) {
            $due = $request->due;
            if ($due === 'today') {
                $query->whereDate('due_date', now());
            } elseif ($due === 'tomorrow') {
                $query->whereDate('due_date', now()->addDay());
            } elseif ($due === 'week') {
                $query->whereBetween('due_date', [now(), now()->addDays(7)]);
            }
        }
        
        // Order by
        $orderBy = $request->order_by ?? 'created_at';
        $orderDir = $request->order_dir ?? 'desc';
        
        $tasks = $query->orderBy($orderBy, $orderDir)->get();
        
        return $this->successResponse(
            data: $tasks
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'priority' => $validated['priority'] ?? 'medium',
            'completed' => false,
            'user_id' => Auth::id()
        ]);
        
        return $this->successResponse(
            data: $task->load('category'),
            message: 'Task created successfully',
            code: 201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        if ($task->user_id !== Auth::id()) {
            return $this->unauthorizedResponse('You are not authorized to view this task');
        }
        
        return $this->successResponse(
            data: $task->load('category')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        if ($task->user_id !== Auth::id()) {
            return $this->unauthorizedResponse('You are not authorized to update this task');
        }
        
        $validated = $request->validated();
        
        $task->update($validated);
        
        return $this->successResponse(
            data: $task->refresh()->load('category'),
            message: 'Task updated successfully'
        );
    }

    /**
     * Toggle the completed status of a task.
     */
    public function toggleComplete(Task $task): JsonResponse
    {
        if ($task->user_id !== Auth::id()) {
            return $this->unauthorizedResponse('You are not authorized to update this task');
        }
        
        $task->completed = !$task->completed;
        $task->save();
        
        return $this->successResponse(
            data: $task->refresh()->load('category'),
            message: $task->completed ? 'Task marked as completed' : 'Task marked as pending'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        if ($task->user_id !== Auth::id()) {
            return $this->unauthorizedResponse('You are not authorized to delete this task');
        }
        
        $task->delete();
        
        return $this->successResponse(
            message: 'Task deleted successfully'
        );
    }
} 