<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TagStoreRequest;
use App\Http\Requests\Admin\TagUpdateRequest;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the tags.
     */
    public function index(Request $request)
    {
        $query = Tag::query()->with('user');
        
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }
        
        $tags = $query->latest()->paginate(10);
        $users = User::all();
        
        return view('admin.tags.index', compact('tags', 'users'));
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create()
    {
        $users = User::all();
        
        return view('admin.tags.create', compact('users'));
    }

    /**
     * Store a newly created tag in storage.
     */
    public function store(TagStoreRequest $request)
    {
        Tag::create($request->validated());
        
        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag created successfully.');
    }

    /**
     * Display the specified tag.
     */
    public function show(Tag $tag)
    {
        $tag->load(['user', 'tasks']);
        
        return view('admin.tags.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified tag.
     */
    public function edit(Tag $tag)
    {
        $users = User::all();
        
        return view('admin.tags.edit', compact('tag', 'users'));
    }

    /**
     * Update the specified tag in storage.
     */
    public function update(TagUpdateRequest $request, Tag $tag)
    {
        $tag->update($request->validated());
        
        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag updated successfully.');
    }

    /**
     * Remove the specified tag from storage.
     */
    public function destroy(Tag $tag)
    {
        // Check if there are associated tasks
        if ($tag->tasks()->count() > 0) {
            // Detach tasks instead of preventing deletion
            $tag->tasks()->detach();
        }
        
        $tag->delete();
        
        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag deleted successfully.');
    }
} 