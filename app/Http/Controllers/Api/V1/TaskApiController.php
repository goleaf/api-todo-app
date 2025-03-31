<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Task\TaskStoreRequest;
use App\Http\Requests\Api\Task\TaskUpdateRequest;
use App\Http\Requests\Api\Task\TaskUpdateTagsRequest;
use App\Services\Api\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskApiController extends ApiController
{
    protected TaskService $service;

    /**
     * TaskApiController constructor.
     */
    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        return $this->service->index($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskStoreRequest $request): JsonResponse
    {
        return $this->service->store($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        return $this->service->show($id, $request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskUpdateRequest $request, int $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->service->destroy($id);
    }

    /**
     * Toggle the completion status of a task.
     */
    public function toggleCompletion(int $id): JsonResponse
    {
        return $this->service->toggleCompletion($id);
    }

    /**
     * Get tasks due today.
     */
    public function dueToday(): JsonResponse
    {
        return $this->service->getDueToday();
    }

    /**
     * Get overdue tasks.
     */
    public function overdue(): JsonResponse
    {
        return $this->service->getOverdue();
    }

    /**
     * Get upcoming tasks.
     */
    public function upcoming(Request $request): JsonResponse
    {
        $days = $request->get('days', 7);

        return $this->service->getUpcoming($days);
    }

    /**
     * Get task statistics.
     */
    public function statistics(): JsonResponse
    {
        return $this->service->getStatistics();
    }

    /**
     * Get all tags for a task.
     */
    public function tags(int $id): JsonResponse
    {
        return $this->service->getTaskTags($id);
    }

    /**
     * Update tags for a task.
     */
    public function updateTags(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'string|max:50',
        ]);
        
        return $this->service->updateTaskTags($id, $validated['tags']);
    }

    /**
     * Bulk operation on task tags.
     */
    public function bulkTagOperation(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'operation' => 'required|string|in:add,remove',
            'tags' => 'required|array',
            'tags.*' => 'string|max:50',
        ]);
        
        return $this->service->bulkTagOperation(
            $id, 
            $validated['operation'], 
            $validated['tags']
        );
    }

    /**
     * Find tasks by tag name.
     */
    public function findByTag(string $tagName, Request $request): JsonResponse
    {
        return $this->service->findTasksByTagName($tagName, $request);
    }
}
