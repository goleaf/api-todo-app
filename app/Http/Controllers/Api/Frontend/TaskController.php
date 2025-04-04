<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends BaseController
{
    /**
     * Display a listing of tasks.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $tasks = Task::where('user_id', Auth::id())
            ->with(['category', 'tags'])
            ->latest()
            ->paginate(10);

        return $this->successResponse($tasks);
    }

    /**
     * Store a newly created task.
     *
     * @param  \App\Http\Requests\TaskRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TaskRequest $request)
    {
        $task = new Task($request->validated());
        $task->user_id = Auth::id();
        $task->save();

        if ($request->has('tags')) {
            $task->tags()->sync($request->tags);
        }

        return $this->successResponse($task->load(['category', 'tags']), 'Task created successfully');
    }

    /**
     * Display the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return $this->successResponse($task->load(['category', 'tags']));
    }

    /**
     * Update the specified task.
     *
     * @param  \App\Http\Requests\TaskRequest  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        if ($request->has('tags')) {
            $task->tags()->sync($request->tags);
        }

        return $this->successResponse($task->load(['category', 'tags']), 'Task updated successfully');
    }

    /**
     * Remove the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return $this->successResponse(null, 'Task deleted successfully');
    }

    /**
     * Toggle task completion status.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleComplete(Task $task)
    {
        $this->authorize('update', $task);

        $task->completed = !$task->completed;
        $task->save();

        return $this->successResponse($task, 'Task status updated successfully');
    }
} 