<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    /**
     * Display a listing of the tags.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Tag::where('user_id', auth()->id());
        
        // Sort options
        $sortField = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        $allowedSortFields = ['name', 'created_at', 'updated_at'];
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
        }
        
        // Optionally include task count
        if ($request->boolean('with_tasks_count')) {
            $query->withCount('tasks');
        }
        
        $tags = $query->get();
        
        return response()->json([
            'data' => $tags
        ]);
    }

    /**
     * Store a newly created tag in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[a-fA-F0-9]{6}$/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tag = new Tag();
        $tag->name = $request->name;
        $tag->color = $request->color ?? '#6b7280';
        $tag->user_id = auth()->id();
        $tag->save();
        
        return response()->json([
            'message' => 'Tag created successfully',
            'data' => $tag
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified tag.
     *
     * @param Tag $tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Tag $tag)
    {
        $this->authorize('view', $tag);
        
        if (request()->boolean('with_tasks')) {
            $tag->load('tasks');
        }
        
        if (request()->boolean('with_tasks_count')) {
            $tag->loadCount('tasks');
        }
        
        return response()->json([
            'data' => $tag
        ]);
    }

    /**
     * Update the specified tag in storage.
     *
     * @param Request $request
     * @param Tag $tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Tag $tag)
    {
        $this->authorize('update', $tag);
        
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[a-fA-F0-9]{6}$/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        if ($request->has('name')) {
            $tag->name = $request->name;
        }
        
        if ($request->has('color')) {
            $tag->color = $request->color;
        }
        
        $tag->save();
        
        return response()->json([
            'message' => 'Tag updated successfully',
            'data' => $tag
        ]);
    }

    /**
     * Remove the specified tag from storage.
     *
     * @param Tag $tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);
        
        // Detach the tag from all tasks
        $tag->tasks()->detach();
        
        $tag->delete();
        
        return response()->json([
            'message' => 'Tag deleted successfully'
        ]);
    }

    /**
     * Get tag suggestions for autocompletion.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggest(Request $request)
    {
        $query = $request->get('query', '');
        
        $tags = Tag::where('user_id', auth()->id())
            ->where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'color']);
        
        return response()->json([
            'data' => $tags
        ]);
    }

    /**
     * Get tasks for a specific tag.
     *
     * @param Tag $tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function tasks(Tag $tag)
    {
        $this->authorize('view', $tag);
        
        $query = $tag->tasks()->with(['category']);
        
        // Apply filters
        if (request()->has('status')) {
            $query->where('status', request('status'));
        }
        
        if (request()->has('priority')) {
            $query->where('priority', request('priority'));
        }
        
        // Apply sorting
        $sortField = request()->get('sort_by', 'created_at');
        $sortDirection = request()->get('sort_direction', 'desc');
        $allowedSortFields = ['title', 'created_at', 'due_date', 'priority', 'status'];
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
        }
        
        $tasks = $query->paginate(10);
        
        return response()->json([
            'data' => $tasks->items(),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total()
            ]
        ]);
    }
}
