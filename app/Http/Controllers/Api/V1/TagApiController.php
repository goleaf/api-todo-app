<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Tag\BatchTagStoreRequest;
use App\Http\Requests\Api\Tag\TagMergeRequest;
use App\Http\Requests\Api\Tag\TagStoreRequest;
use App\Http\Requests\Api\Tag\TagSuggestionsRequest;
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
    public function merge(TagMergeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        return $this->service->mergeTags(
            $validated['source_tag_id'],
            $validated['target_tag_id']
        );
    }

    /**
     * Get tag suggestions for autocomplete.
     */
    public function suggestions(TagSuggestionsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $limit = $validated['limit'] ?? 10;
        
        return $this->service->getSuggestions($validated['query'], $limit);
    }

    /**
     * Create multiple tags in a single operation.
     */
    public function batchCreate(BatchTagStoreRequest $request): JsonResponse
    {
        return $this->service->batchCreate($request->validated()['tags']);
    }
}
