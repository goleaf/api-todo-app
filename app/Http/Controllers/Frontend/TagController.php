<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    /**
     * Display a listing of tags.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $tags = Tag::where('user_id', Auth::id())
            ->withCount(['tasks' => function ($query) {
                $query->where('completed', false);
            }])
            ->latest()
            ->paginate(10);

        return view('frontend.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new tag.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('frontend.tags.create');
    }

    /**
     * Store a newly created tag.
     *
     * @param  \App\Http\Requests\TagRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TagRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        Tag::create($validated);

        return redirect()->route('tags.index')
            ->with('success', 'Tag created successfully.');
    }

    /**
     * Display the specified tag.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\View\View
     */
    public function show(Tag $tag)
    {
        $this->authorize('view', $tag);
        
        $tag->load(['tasks' => function ($query) {
            $query->latest();
        }]);

        return view('frontend.tags.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified tag.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\View\View
     */
    public function edit(Tag $tag)
    {
        $this->authorize('update', $tag);
        return view('frontend.tags.edit', compact('tag'));
    }

    /**
     * Update the specified tag.
     *
     * @param  \App\Http\Requests\TagRequest  $request
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TagRequest $request, Tag $tag)
    {
        $this->authorize('update', $tag);
        
        $tag->update($request->validated());

        return redirect()->route('tags.index')
            ->with('success', 'Tag updated successfully.');
    }

    /**
     * Remove the specified tag.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);
        
        $tag->delete();

        return redirect()->route('tags.index')
            ->with('success', 'Tag deleted successfully.');
    }
} 