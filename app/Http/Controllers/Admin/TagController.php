<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Tag\StoreTagRequest;
use App\Http\Requests\Admin\Tag\UpdateTagRequest;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends BaseController
{
    /**
     * Display a listing of the tags.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $query = Tag::with('user');
        
        // Filter by user if specified
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Search by name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Order by
        $orderBy = $request->order_by ?? 'name';
        $direction = $request->direction ?? 'asc';
        $query->orderBy($orderBy, $direction);
        
        $tags = $query->paginate(15);
        $users = User::all(['id', 'name']);
        
        return view('pages.admin.tags.index', compact('tags', 'users'));
    }

    /**
     * Show the form for creating a new tag.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        $users = User::all(['id', 'name']);
        
        return view('pages.admin.tags.create', compact('users'));
    }

    /**
     * Store a newly created tag in storage.
     *
     * @param  \App\Http\Requests\Admin\Tag\StoreTagRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTagRequest $request)
    {
        $tag = Tag::create($request->validated());

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag created successfully.');
    }

    /**
     * Display the specified tag.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\View\View
     */
    public function show(Tag $tag): View
    {
        $tag->load(['user', 'tasks']);
        
        return view('pages.admin.tags.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified tag.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\View\View
     */
    public function edit(Tag $tag): View
    {
        $users = User::all(['id', 'name']);
        
        return view('pages.admin.tags.edit', compact('tag', 'users'));
    }

    /**
     * Update the specified tag in storage.
     *
     * @param  \App\Http\Requests\Admin\Tag\UpdateTagRequest  $request
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $tag->update($request->validated());

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag updated successfully.');
    }

    /**
     * Remove the specified tag from storage.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tag $tag)
    {
        // Detach from all tasks before deleting
        $tag->tasks()->detach();
        $tag->delete();

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag deleted successfully.');
    }
    
    /**
     * Display a listing of tasks belonging to the specified tag.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\View\View
     */
    public function tagTasks(Tag $tag): View
    {
        $tasks = $tag->tasks()->with(['category', 'user'])->paginate(15);
        
        return view('pages.admin.tags.tasks', compact('tag', 'tasks'));
    }
} 