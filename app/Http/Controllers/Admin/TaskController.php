<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TaskPriority;
use App\Http\Requests\Admin\TaskStoreRequest;
use App\Http\Requests\Admin\TaskUpdateRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends AdminController
{
    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request)
    {
        $query = Task::query()->with(['user', 'category']);
        
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }
        
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }
        
        if ($status = $request->input('status')) {
            $completed = $status === 'completed';
            $query->where('completed', $completed);
        }
        
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }
        
        $tasks = $query->latest()->fastPaginate(10);
        $users = User::all();
        $categories = Category::all();
        $priorities = TaskPriority::cases();
        
        return view('admin.tasks.index', compact('tasks', 'users', 'categories', 'priorities'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $users = User::all();
        $priorities = TaskPriority::cases();
        
        // Get categories only after user is selected (this will be handled with JS)
        $categories = [];
        
        // All tags (will be filtered by user with JS)
        $tags = [];
        
        $isEdit = false;
        
        return view('admin.tasks.form', compact('users', 'categories', 'priorities', 'tags', 'isEdit'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(TaskStoreRequest $request)
    {
        $data = $request->validated();
        
        // Create task with main attributes
        $task = Task::create($data);
        
        // Attach tags if any
        if (!empty($data['tags'])) {
            $task->tags()->attach($data['tags']);
        }
        
        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        // Ensure all needed relationships are loaded
        $task->load(['user', 'category', 'tags']);
        
        // Make sure tags is a collection even if it's empty
        if (!$task->tags) {
            $task->setRelation('tags', collect([]));
        }
        
        return view('admin.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $users = User::all();
        $priorities = TaskPriority::cases();
        
        // Get categories for the task's user
        $categories = Category::where('user_id', $task->user_id)->get();
        
        // Get tags for the task's user
        $tags = Tag::where('user_id', $task->user_id)->get();
        
        // Load task tags relation if not already loaded
        if (!$task->relationLoaded('tags')) {
            $task->load('tags');
        }
        
        $isEdit = true;
        
        return view('admin.tasks.form', compact('task', 'users', 'categories', 'priorities', 'tags', 'isEdit'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(TaskUpdateRequest $request, Task $task)
    {
        $data = $request->validated();
        
        // Update task with main attributes
        $task->update($data);
        
        // Sync tags if present in the request
        if (isset($data['tags'])) {
            $task->tags()->sync($data['tags']);
        }
        
        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        // Detach all tags
        $task->tags()->detach();
        
        // Delete the task
        $task->delete();
        
        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
    
    /**
     * Toggle the completion status of a task.
     */
    public function toggleCompletion(Task $task)
    {
        $task->toggleCompletion();
        
        return back()->with('success', 'Task status updated successfully.');
    }
    
    /**
     * Get categories for a specific user (for AJAX).
     */
    public function getCategoriesForUser(User $user)
    {
        $categories = Category::where('user_id', $user->id)->get();
        
        return response()->json($categories);
    }
    
    /**
     * Get tags for a specific user (for AJAX).
     */
    public function getTagsForUser(User $user)
    {
        $tags = Tag::where('user_id', $user->id)->get();
        
        return response()->json($tags);
    }
} 