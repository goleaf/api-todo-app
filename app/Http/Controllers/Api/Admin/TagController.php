<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends BaseController
{
    /**
     * Display a listing of tags.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $tags = Tag::withCount('tasks')
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
        $tag = Tag::create($request->validated());

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
        $tasks = $tag->tasks()
            ->with(['user', 'category'])
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
        $tag->delete();

        return $this->successResponse(null, 'Tag deleted successfully');
    }

    /**
     * Get tags by user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function userTags(User $user)
    {
        $tags = Tag::where('user_id', $user->id)
            ->withCount('tasks')
            ->latest()
            ->paginate(10);

        return $this->successResponse($tags);
    }
} 