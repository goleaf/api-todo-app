<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends BaseController
{
    /**
     * Display a listing of tags.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $tags = Tag::where('user_id', Auth::id())
            ->withCount('tasks')
            ->latest()
            ->paginate(10);

        return $this->successResponse($tags);
    }

    /**
     * Store a newly created tag.
     *
     * @param  \App\Http\Requests\TagRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TagRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        $tag = Tag::create($validated);

        return $this->successResponse($tag, 'Tag created successfully');
    }

    /**
     * Display the specified tag.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Tag $tag)
    {
        $this->authorize('view', $tag);

        $tasks = $tag->tasks()
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return $this->successResponse([
            'tag' => $tag,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Update the specified tag.
     *
     * @param  \App\Http\Requests\TagRequest  $request
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TagRequest $request, Tag $tag)
    {
        $this->authorize('update', $tag);

        $tag->update($request->validated());

        return $this->successResponse($tag, 'Tag updated successfully');
    }

    /**
     * Remove the specified tag.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);

        $tag->delete();

        return $this->successResponse(null, 'Tag deleted successfully');
    }

    /**
     * Suggest tags based on query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggest(Request $request)
    {
        $query = $request->get('query', '');
        $limit = $request->get('limit', 5);
        
        $tags = Tag::where('user_id', Auth::id())
            ->where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name']);
            
        return $this->successResponse($tags);
    }
} 