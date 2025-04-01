<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Task\StoreTaskRequest;
use App\Http\Requests\Admin\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index(Request $request)
    {
        $query = Task::where('user_id', auth()->id())
            ->with(['category', 'tags']);
            
        // Filter by category if provided
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        // Filter by completion status
        if ($request->has('completed')) {
            $query->where('completed', $request->completed == 'true');
        }
        
        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Sort options
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $query->orderBy($sort, $direction);
        
        $tasks = $query->paginate(10)->withQueryString();
        
        $categories = Category::where('user_id', auth()->id())->get();
        
        return view('admin.tasks.index', compact('tasks', 'categories'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $categories = Category::where('user_id', auth()->id())->get();
        $tags = Tag::where('user_id', auth()->id())->get();
        $priorities = [
            Task::PRIORITY_LOW => 'Low',
            Task::PRIORITY_MEDIUM => 'Medium',
            Task::PRIORITY_HIGH => 'High'
        ];
        
        return view('admin.tasks.create', compact('categories', 'tags', 'priorities'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = new Task($request->validated());
        $task->user_id = auth()->id();
        $task->save();
        
        // Attach tags if provided
        if ($request->has('tags')) {
            $task->tags()->attach($request->tags);
        }
        
        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task created successfully');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        
        $task->load(['category', 'tags', 'timeEntries', 'attachments']);
        
        return view('admin.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        
        $categories = Category::where('user_id', auth()->id())->get();
        $tags = Tag::where('user_id', auth()->id())->get();
        $priorities = [
            Task::PRIORITY_LOW => 'Low',
            Task::PRIORITY_MEDIUM => 'Medium',
            Task::PRIORITY_HIGH => 'High'
        ];
        
        $task->load('tags');
        
        return view('admin.tasks.edit', compact('task', 'categories', 'tags', 'priorities'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        
        $task->update($request->validated());
        
        // Sync tags
        if ($request->has('tags')) {
            $task->tags()->sync($request->tags);
        } else {
            $task->tags()->detach();
        }
        
        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task updated successfully');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        
        $task->delete();
        
        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully');
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
