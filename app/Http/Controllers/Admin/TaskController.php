<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Task\StoreTaskRequest;
use App\Http\Requests\Admin\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request): View
    {
        $query = Task::with(['category', 'tags', 'user']);
        
        // Filter by user if specified
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by completion status
        if ($request->has('completed')) {
            $query->where('completed', $request->completed);
        }
        
        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Search by title or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Order by
        $orderBy = $request->order_by ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $query->orderBy($orderBy, $direction);
        
        $tasks = $query->paginate(15);
        $users = User::all(['id', 'name']);
        $categories = Category::all(['id', 'name']);
        
        return view('pages.admin.tasks.index', compact('tasks', 'users', 'categories'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $users = User::all(['id', 'name']);
        $categories = Category::all(['id', 'name']);
        $tags = Tag::all(['id', 'name']);
        
        return view('pages.admin.tasks.create', compact('users', 'categories', 'tags'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());
        
        // Attach tags if provided
        if ($request->has('tags')) {
            $task->tags()->attach($request->tags);
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $task->load(['category', 'tags', 'user', 'timeEntries', 'attachments']);
        
        return view('pages.admin.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $users = User::all(['id', 'name']);
        $categories = Category::all(['id', 'name']);
        $tags = Tag::all(['id', 'name']);
        $task->load('tags');
        
        return view('pages.admin.tasks.edit', compact('task', 'users', 'categories', 'tags'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        
        // Sync tags if provided
        if ($request->has('tags')) {
            $task->tags()->sync($request->tags);
        } else {
            $task->tags()->detach();
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
    
    /**
     * Toggle the completed status of the task.
     */
    public function toggle(Task $task)
    {
        $this->authorize('update', $task);
        
        $task->completed = !$task->completed;
        $task->save();
        
        return back()->with('success', 'Task status updated');
    }
}
