<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Todo\TaskStoreRequest;
use App\Http\Requests\Api\Todo\TaskUpdateRequest;
use App\Services\Api\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskApiController extends Controller
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
    public function toggleComplete(int $id): JsonResponse
    {
        return $this->service->toggleComplete($id);
    }

    /**
     * Get task statistics for the authenticated user.
     */
    public function statistics(): JsonResponse
    {
        return $this->service->getStatistics();
    }
} 