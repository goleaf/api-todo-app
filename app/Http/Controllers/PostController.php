<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the published posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::published()->latest()->fastPaginate(10);
        
        return response()->json([
            'status' => 'success',
            'data' => $posts
        ]);
    }
    
    /**
     * Display a listing of all draft posts.
     *
     * @return \Illuminate\Http\Response
     */
    public function drafts()
    {
        $drafts = Post::onlyDrafts()->where('user_id', Auth::id())->latest()->fastPaginate(10);
        
        return response()->json([
            'status' => 'success',
            'data' => $drafts
        ]);
    }

    /**
     * Store a newly created post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'publish' => 'boolean'
        ]);

        $slug = Str::slug($validated['title']);
        
        // Check if we should publish immediately or save as draft
        if ($request->input('publish', false)) {
            // Create and publish
            $post = Post::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'slug' => $slug,
                'user_id' => Auth::id(),
                'is_published' => true
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Post published successfully',
                'data' => $post
            ], 201);
        }
            // Create as draft
            $post = Post::createDraft([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'slug' => $slug,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Draft saved successfully',
                'data' => $post
            ], 201);
        
    }

    /**
     * Display the specified post.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        
        return response()->json([
            'status' => 'success',
            'data' => $post
        ]);
    }
    
    /**
     * Display the draft version of a post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showDraft($id)
    {
        $post = Post::onlyDrafts()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        return response()->json([
            'status' => 'success',
            'data' => $post
        ]);
    }

    /**
     * Update the specified post.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        
        // Check user ownership
        if ($post->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'publish' => 'boolean'
        ]);
        
        $slug = Str::slug($validated['title']);
        
        if ($request->input('publish', false)) {
            // Update and publish
            $post->update([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'slug' => $slug,
                'is_published' => true
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Post updated and published',
                'data' => $post
            ]);
        }
            // Update as draft
            $post->updateAsDraft([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'slug' => $slug
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Draft updated successfully',
                'data' => $post
            ]);
        
    }
    
    /**
     * Publish the draft post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function publish($id)
    {
        $draft = Post::onlyDrafts()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $published = $draft->publish();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Draft published successfully',
            'data' => $published
        ]);
    }

    /**
     * Remove the specified post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::withDrafts()->find($id);
        
        if (!$post) {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found'
            ], 404);
        }
        
        // Check user ownership
        if ($post->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $post->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted successfully'
        ]);
    }
}
