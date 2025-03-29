<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::forUser(Auth::id())
            ->with('category');
        
        // Filter by category
        if ($request->has('category')) {
            $query->inCategory($request->input('category'));
        }
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->input('status') === 'completed') {
                $query->completed();
            } elseif ($request->input('status') === 'incomplete') {
                $query->incomplete();
            }
        }
        
        // Filter by priority
        if ($request->has('priority')) {
            $query->withPriority($request->input('priority'));
        }
        
        // Sort by due date
        if ($request->has('sort') && $request->input('sort') === 'due_date') {
            $query->orderBy('due_date', 'asc');
        } else {
            $query->latest();
        }
        
        $tasks = $query->get();
        
        return response()->json(['data' => $tasks]);
    }

    /**
     * Search tasks by term.
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);
        
        $term = $request->input('q');
        
        $tasks = Task::forUser(Auth::id())
            ->search($term)
            ->with('category')
            ->get();
        
        return response()->json(['data' => $tasks]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|integer|between:1,3',
            'category_id' => 'required|exists:categories,id',
            'progress' => 'nullable|integer|between:0,100',
        ]);
        
        // Ensure the category belongs to the authenticated user
        $category = \App\Models\Category::findOrFail($validated['category_id']);
        if ($category->user_id !== Auth::id()) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        
        $task = new Task($validated);
        $task->user_id = Auth::id();
        $task->save();
        
        return response()->json(['data' => $task], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $task->load('category');
        
        return response()->json(['data' => $task]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|integer|between:1,3',
            'category_id' => 'sometimes|exists:categories,id',
            'completed' => 'sometimes|boolean',
            'progress' => 'nullable|integer|between:0,100',
        ]);
        
        // If category_id is provided, ensure it belongs to the authenticated user
        if (isset($validated['category_id'])) {
            $category = \App\Models\Category::findOrFail($validated['category_id']);
            if ($category->user_id !== Auth::id()) {
                return response()->json(['message' => 'Category not found'], 404);
            }
        }
        
        // Handle completed status
        if (isset($validated['completed']) && $validated['completed'] && !$task->completed) {
            $task->completed_at = now();
            $validated['progress'] = 100;
        } elseif (isset($validated['completed']) && !$validated['completed'] && $task->completed) {
            $task->completed_at = null;
        }
        
        $task->update($validated);
        
        return response()->json(['data' => $task]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        $task->delete();
        
        return response()->json(null, 204);
    }

    /**
     * Mark a task as complete or incomplete.
     */
    public function toggleComplete(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        if ($task->completed) {
            $task->markAsIncomplete();
        } else {
            $task->markAsComplete();
        }
        
        return response()->json(['data' => $task]);
    }
} 