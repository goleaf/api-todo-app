<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index()
    {
        $query = Task::with(['category', 'tags'])
            ->where('user_id', Auth::id());

        // Filter by category
        if (request()->has('category') && request('category') != '') {
            $query->where('category_id', request('category'));
        }

        // Filter by tag
        if (request()->has('tag') && request('tag') != '') {
            $query->whereHas('tags', function($q) {
                $q->where('tags.id', request('tag'));
            });
        }

        // Filter by status
        if (request()->has('status') && request('status') != '') {
            $query->where('status', request('status'));
        }

        // Filter by priority
        if (request()->has('priority') && request('priority') != '') {
            // Convert string priority to numeric value
            $priorityValue = match(request('priority')) {
                'low' => Task::PRIORITY_LOW,
                'medium' => Task::PRIORITY_MEDIUM,
                'high' => Task::PRIORITY_HIGH,
                default => null,
            };
            
            if ($priorityValue) {
                $query->where('priority', $priorityValue);
            }
        }

        // Filter by due date
        if (request()->has('due_date') && request('due_date') != '') {
            switch (request('due_date')) {
                case 'overdue':
                    $query->overdue();
                    break;
                case 'today':
                    $query->dueToday();
                    break;
                case 'tomorrow':
                    $query->whereDate('due_date', now()->addDay());
                    break;
                case 'this_week':
                    $query->dueThisWeek();
                    break;
                case 'next_week':
                    $query->whereDate('due_date', '>=', now()->addWeek()->startOfWeek())
                          ->whereDate('due_date', '<=', now()->addWeek()->endOfWeek());
                    break;
            }
        }

        // Search by title or description
        if (request()->has('search') && request('search') != '') {
            $searchTerm = '%' . request('search') . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });
        }

        $tasks = $query->latest()->paginate(10);
        $categories = Category::where('user_id', Auth::id())->get();
        $tags = Tag::where('user_id', Auth::id())->get();
        $selectedTag = request('tag');
        
        // Get urgent tasks (overdue or due within 2 days)
        $urgentTasks = $this->getUrgentTasks();

        return view('pages.tasks.index', compact('tasks', 'categories', 'tags', 'selectedTag', 'urgentTasks'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        $categories = Category::where('user_id', Auth::id())->get();
        $tags = Tag::where('user_id', Auth::id())->get(['id', 'name']);

        return view('pages.tasks.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
        ]);

        $task = new Task($validated);
        $task->user_id = Auth::id();
        $task->save();

        if ($request->has('tags')) {
            $tagIds = $this->processTagInput($request->tags);
            $task->tags()->sync($tagIds);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        
        $task->load(['category', 'tags', 'timeEntries', 'attachments']);
        
        return view('pages.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        $categories = Category::where('user_id', Auth::id())->get();
        $tags = Tag::where('user_id', Auth::id())->get(['id', 'name']);
        $selectedTags = $task->tags->map(function($tag) {
            return [
                'id' => $tag->id,
                'name' => $tag->name
            ];
        })->toArray();

        return view('pages.tasks.edit', compact('task', 'categories', 'tags', 'selectedTags'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update($validated);

        if ($request->has('tags')) {
            $tagIds = $this->processTagInput($request->tags);
            $task->tags()->sync($tagIds);
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Toggle the completed status of a task.
     */
    public function toggleComplete(Task $task)
    {
        $this->authorize('update', $task);

        $task->toggle();

        return response()->json(['success' => true]);
    }

    /**
     * Display tasks due today.
     */
    public function dueToday()
    {
        $tasks = Task::with(['category', 'tags'])
            ->where('user_id', Auth::id())
            ->dueToday()
            ->latest()
            ->paginate(10);

        $categories = Category::where('user_id', Auth::id())->get();
        $tags = Tag::where('user_id', Auth::id())->get();
        $selectedTag = request('tag');
        $urgentTasks = $this->getUrgentTasks();

        return view('pages.tasks.index', compact('tasks', 'categories', 'tags', 'selectedTag', 'urgentTasks'));
    }

    /**
     * Display tasks due this week.
     */
    public function dueThisWeek()
    {
        $tasks = Task::with(['category', 'tags'])
            ->where('user_id', Auth::id())
            ->dueThisWeek()
            ->latest()
            ->paginate(10);

        $categories = Category::where('user_id', Auth::id())->get();
        $tags = Tag::where('user_id', Auth::id())->get();
        $selectedTag = request('tag');
        $urgentTasks = $this->getUrgentTasks();

        return view('pages.tasks.index', compact('tasks', 'categories', 'tags', 'selectedTag', 'urgentTasks'));
    }

    /**
     * Display overdue tasks.
     */
    public function overdue()
    {
        $tasks = Task::with(['category', 'tags'])
            ->where('user_id', Auth::id())
            ->overdue()
            ->latest()
            ->paginate(10);

        $categories = Category::where('user_id', Auth::id())->get();
        $tags = Tag::where('user_id', Auth::id())->get();
        $selectedTag = request('tag');
        $urgentTasks = $this->getUrgentTasks();

        return view('pages.tasks.index', compact('tasks', 'categories', 'tags', 'selectedTag', 'urgentTasks'));
    }

    /**
     * Display completed tasks.
     */
    public function completed()
    {
        $tasks = Task::with(['category', 'tags'])
            ->where('user_id', Auth::id())
            ->completed()
            ->latest()
            ->paginate(10);

        $categories = Category::where('user_id', Auth::id())->get();
        $tags = Tag::where('user_id', Auth::id())->get();
        $selectedTag = request('tag');
        $urgentTasks = $this->getUrgentTasks();

        return view('pages.tasks.index', compact('tasks', 'categories', 'tags', 'selectedTag', 'urgentTasks'));
    }

    /**
     * Get urgent tasks (overdue or due within 2 days).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getUrgentTasks()
    {
        return Task::where('user_id', Auth::id())
            ->where('completed', false)
            ->where(function($query) {
                $query->where('due_date', '<', now()) // Overdue
                      ->orWhereBetween('due_date', [now(), now()->addDays(2)]); // Due in next 2 days
            })
            ->orderBy('due_date')
            ->limit(5)
            ->get();
    }

    /**
     * Process tag input to handle new tags.
     *
     * @param array $tags
     * @return array
     */
    protected function processTagInput($tags)
    {
        $tagIds = [];
        
        foreach ($tags as $tagInput) {
            // Check if this is a newly created tag (format: new_timestamp)
            if (is_string($tagInput) && strpos($tagInput, 'new_') === 0) {
                // Extract the tag name from the request
                $tagName = request('new_tag_' . $tagInput);
                
                if (!$tagName) {
                    continue;
                }
                
                // Create a new tag
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName, 'user_id' => Auth::id()],
                    ['color' => '#6b7280']
                );
                
                $tagIds[] = $tag->id;
            } else {
                // This is an existing tag ID
                $tagIds[] = $tagInput;
            }
        }
        
        return $tagIds;
    }
} 