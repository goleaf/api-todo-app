<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Category\CategoryStoreRequest;
use App\Http\Requests\Api\Category\CategoryUpdateRequest;
use App\Services\Api\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    protected CategoryService $service;

    /**
     * CategoryApiController constructor.
     */
    public function __construct(CategoryService $service)
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
    public function store(CategoryStoreRequest $request): JsonResponse
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
    public function update(CategoryUpdateRequest $request, int $id): JsonResponse
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
     * Get task counts for each category.
     */
    public function taskCounts(): JsonResponse
    {
        return $this->service->getTaskCounts();
    }
} 