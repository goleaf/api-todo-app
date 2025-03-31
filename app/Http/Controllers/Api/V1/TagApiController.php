<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Tag\TagStoreRequest;
use App\Http\Requests\Api\Tag\TagUpdateRequest;
use App\Services\Api\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagApiController extends ApiController
{
    protected TagService $service;

    /**
     * TagApiController constructor.
     */
    public function __construct(TagService $service)
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
    public function store(TagStoreRequest $request): JsonResponse
    {
        return $this->service->store($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return $this->service->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TagUpdateRequest $request, int $id): JsonResponse
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
     * Get popular tags for the user.
     */
    public function popular(int $limit = 10): JsonResponse
    {
        return $this->service->popular($limit);
    }

    /**
     * Get all tasks with a specific tag.
     */
    public function tasks(int $id): JsonResponse
    {
        return $this->service->getTasks($id);
    }

    /**
     * Get task counts by tag.
     */
    public function taskCounts(): JsonResponse
    {
        return $this->service->getTaskCounts();
    }

    /**
     * Merge two tags.
     */
    public function merge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'source_tag_id' => 'required|integer|exists:tags,id',
            'target_tag_id' => 'required|integer|exists:tags,id|different:source_tag_id',
        ]);
        
        return $this->service->mergeTags(
            $validated['source_tag_id'],
            $validated['target_tag_id']
        );
    }
}
