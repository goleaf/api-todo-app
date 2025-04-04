<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::where('user_id', Auth::id())
            ->withCount('tasks')
            ->orderBy('name')
            ->paginate(10);
        
        // Get all user tags with task counts for the tag cloud
        $tagCloud = Tag::where('user_id', Auth::id())
            ->withCount('tasks')
            ->orderByDesc('tasks_count')
            ->get();
        
        return view('pages.tags.index', compact('tags', 'tagCloud'));
    }
    
    public function create()
    {
        return view('pages.tags.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,NULL,id,user_id,' . Auth::id(),
            'description' => 'nullable|string|max:1000'
        ]);
        
        $tag = new Tag($validated);
        $tag->user_id = Auth::id();
        $tag->save();
        
        return redirect()->route('tags.index')
            ->with('success', __('Tag created successfully.'));
    }
    
    public function show(Tag $tag)
    {
        $this->authorize('view', $tag);
        
        $tag->load(['tasks' => function ($query) {
            $query->with(['category', 'tags'])
                ->latest();
        }]);
        
        return view('pages.tags.show', compact('tag'));
    }
    
    public function edit(Tag $tag)
    {
        $this->authorize('update', $tag);
        
        return view('pages.tags.edit', compact('tag'));
    }
    
    public function update(Request $request, Tag $tag)
    {
        $this->authorize('update', $tag);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id . ',id,user_id,' . Auth::id(),
            'description' => 'nullable|string|max:1000'
        ]);
        
        $tag->update($validated);
        
        return redirect()->route('tags.index')
            ->with('success', __('Tag updated successfully.'));
    }
    
    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);
        
        $tag->delete();
        
        return redirect()->route('tags.index')
            ->with('success', __('Tag deleted successfully.'));
    }
} 