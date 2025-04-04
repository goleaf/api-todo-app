<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends BaseController
{
    /**
     * Display a listing of tasks.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $tasks = Task::with(['user', 'category', 'tags'])
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
        $task->save();

        if ($request->has('tags')) {
            $task->tags()->sync($request->tags);
        }

        return $this->successResponse($task->load(['user', 'category', 'tags']), 'Task created successfully');
    }

    /**
     * Display the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Task $task)
    {
        return $this->successResponse($task->load(['user', 'category', 'tags']));
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
        $task->update($request->validated());

        if ($request->has('tags')) {
            $task->tags()->sync($request->tags);
        }

        return $this->successResponse($task->load(['user', 'category', 'tags']), 'Task updated successfully');
    }

    /**
     * Remove the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Task $task)
    {
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
        $task->completed = !$task->completed;
        $task->save();

        return $this->successResponse($task, 'Task status updated successfully');
    }

    /**
     * Get tasks by user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function userTasks(User $user)
    {
        $tasks = Task::where('user_id', $user->id)
            ->with(['category', 'tags'])
            ->latest()
            ->paginate(10);

        return $this->successResponse($tasks);
    }

    /**
     * Get tasks by category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function categoryTasks(Category $category)
    {
        $tasks = Task::where('category_id', $category->id)
            ->with(['user', 'tags'])
            ->latest()
            ->paginate(10);

        return $this->successResponse($tasks);
    }
} 