<?php

namespace App\Services\Api;

use App\Enums\TaskPriority;
use App\Enums\TaskProgressStatus;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    use ApiResponse;

    /**
     * Display a listing of tasks with filtering capabilities.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $query = Task::forUser($userId);

        // Apply filters
        if ($request->has('category_id')) {
            $query->inCategory($request->category_id);
        }

        if ($request->has('completed')) {
            $completed = filter_var($request->completed, FILTER_VALIDATE_BOOLEAN);
            $query = $completed ? $query->completed() : $query->incomplete();
        }

        if ($request->has('priority')) {
            $priority = TaskPriority::fromValueOrDefault((int) $request->priority);
            $query->withPriority($priority->value);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('due_date')) {
            $query->dueOn($request->due_date);
        }

        if ($request->has('tag')) {
            $query->withTag($request->tag);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        if ($sortBy === 'priority') {
            $query->orderByPriority();
        } elseif ($sortBy === 'due_date') {
            $query->orderByDueDate();
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        // For tests, if we're not paginating, just get all tasks
        if ($request->has('no_pagination')) {
            $tasks = $query->get();
            return $this->successResponse($tasks);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $tasks = $query->paginate($perPage);
        
        // For Laravel's response macros, we need to transform the paginator to an array
        return $this->successResponse($tasks->items());
    }

    /**
     * Store a new task.
     */
    public function store(array $data): JsonResponse
    {
        $data['user_id'] = Auth::id();
        
        // Ensure 'completed' is set to false by default if not provided
        if (!isset($data['completed'])) {
            $data['completed'] = false;
        }

        // Set default priority if not provided
        if (isset($data['priority']) && is_numeric($data['priority'])) {
            $data['priority'] = (int)$data['priority'];
        }

        // Set default progress
        if (!isset($data['progress'])) {
            $data['progress'] = 0;
        }
        
        // Extract tags to handle separately
        $tagNames = $data['tags'] ?? [];
        unset($data['tags']);

        $task = Task::create($data);
        
        // Handle tags
        if (!empty($tagNames)) {
            $this->syncTagsForTask($task, $tagNames);
        }

        return $this->createdResponse($task, __('messages.task.created'));
    }

    /**
     * Display a specific task.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $userId = Auth::id();
        $task = Task::forUser($userId)->find($id);

        if (! $task) {
            return $this->errorResponse(__('validation.task.not_found'), 404);
        }

        return $this->successResponse($task);
    }

    /**
     * Update a task.
     */
    public function update(int $id, array $data): JsonResponse
    {
        $userId = Auth::id();
        $task = Task::forUser($userId)->find($id);

        if (! $task) {
            return $this->errorResponse(__('validation.task.not_found'), 404);
        }
        
        // Extract tags to handle separately
        $tagNames = null;
        if (array_key_exists('tags', $data)) {
            $tagNames = $data['tags'] ?? [];
            unset($data['tags']);
        }

        $task->update($data);
        
        // Handle tags if provided
        if ($tagNames !== null) {
            $this->syncTagsForTask($task, $tagNames);
        }

        return $this->successResponse($task, __('messages.task.updated'));
    }

    /**
     * Delete a task.
     */
    public function destroy(int $id): JsonResponse
    {
        $userId = Auth::id();
        $task = Task::forUser($userId)->find($id);

        if (! $task) {
            return $this->errorResponse(__('validation.task.not_found'), 404);
        }

        $task->delete();

        return $this->noContentResponse(__('messages.task.deleted'));
    }

    /**
     * Toggle a task's completion status.
     */
    public function toggleCompletion(int $id): JsonResponse
    {
        $userId = Auth::id();
        $task = Task::forUser($userId)->find($id);

        if (! $task) {
            return $this->errorResponse(__('validation.task.not_found'), 404);
        }

        $task->toggleCompletion();

        $message = $task->completed
            ? __('messages.task.marked_complete')
            : __('messages.task.marked_incomplete');

        return $this->successResponse($task, $message);
    }

    /**
     * Get tasks due today.
     */
    public function getDueToday(): JsonResponse
    {
        $userId = Auth::id();
        $tasks = Task::forUser($userId)->dueToday()->get();

        return $this->successResponse($tasks);
    }

    /**
     * Get overdue tasks.
     */
    public function getOverdue(): JsonResponse
    {
        $userId = Auth::id();
        $tasks = Task::forUser($userId)->overdue()->get();

        return $this->successResponse($tasks);
    }

    /**
     * Get upcoming tasks.
     */
    public function getUpcoming(): JsonResponse
    {
        $userId = Auth::id();
        $days = request('days', 7);
        $tasks = Task::forUser($userId)->upcoming($days)->get();

        return $this->successResponse($tasks);
    }

    /**
     * Get task statistics.
     */
    public function getStatistics(): JsonResponse
    {
        $user = Auth::user();
        $statistics = $user->getTaskStatistics();

        return $this->successResponse($statistics);
    }
    
    /**
     * Get all tags for a task.
     */
    public function getTaskTags(int $taskId): JsonResponse
    {
        $userId = Auth::id();
        $task = Task::forUser($userId)->find($taskId);

        if (!$task) {
            return $this->errorResponse(__('validation.task.not_found'), 404);
        }

        $tags = $task->tags()->get();
        
        return $this->successResponse($tags);
    }
    
    /**
     * Update tags for a task.
     */
    public function updateTaskTags(int $taskId, array $tagNames): JsonResponse
    {
        $userId = Auth::id();
        $task = Task::forUser($userId)->find($taskId);

        if (!$task) {
            return $this->errorResponse(__('validation.task.not_found'), 404);
        }
        
        $this->syncTagsForTask($task, $tagNames);
        
        return $this->successResponse(
            $task->load('tags'), 
            __('messages.task.tags_updated')
        );
    }
    
    /**
     * Perform bulk operations on task tags (add or remove).
     */
    public function bulkTagOperation(int $taskId, string $operation, array $tagNames): JsonResponse
    {
        $userId = Auth::id();
        $task = Task::forUser($userId)->find($taskId);

        if (!$task) {
            return $this->errorResponse(__('validation.task.not_found'), 404);
        }
        
        // Skip if no tags provided
        if (empty($tagNames)) {
            return $this->successResponse($task->load('tags'));
        }
        
        // Process tags and get their IDs
        $tagIds = $this->getOrCreateTagIds($tagNames, $userId);
        
        // Perform the operation
        if ($operation === 'add') {
            // Use the new Task model method to add tags
            $task->addTags($tagIds);
            $message = __('messages.task.tags_added');
        } else {
            // Use the new Task model method to remove tags
            $task->removeTags($tagIds);
            $message = __('messages.task.tags_removed');
        }
        
        // Update tag usage counts
        $this->updateTagUsageCount($userId);
        
        return $this->successResponse(
            $task->load('tags'),
            $message
        );
    }
    
    /**
     * Get or create tags and return their IDs.
     */
    protected function getOrCreateTagIds(array $tagNames, int $userId): array
    {
        $tagIds = [];
        
        foreach ($tagNames as $name) {
            // Skip empty tag names
            if (empty(trim($name))) {
                continue;
            }
            
            // Find or create the tag using the enhanced model method
            $tag = \App\Models\Tag::findOrCreateForUser($name, $userId);
            
            $tagIds[] = $tag->id;
        }
        
        return $tagIds;
    }
    
    /**
     * Sync tags for a task.
     */
    protected function syncTagsForTask(Task $task, array $tagNames): void
    {
        $userId = Auth::id();
        
        // Get or create tags
        $tagIds = $this->getOrCreateTagIds($tagNames, $userId);
        
        // Sync tags
        $task->tags()->sync($tagIds);
        
        // Update usage counts
        $this->updateTagUsageCount($userId);
    }
    
    /**
     * Update tag usage counts.
     */
    protected function updateTagUsageCount(int $userId): void
    {
        // Get all user tags
        $tags = \App\Models\Tag::forUser($userId)->get();
        
        foreach ($tags as $tag) {
            // Count tasks with this tag
            $count = $tag->tasks()->count();
            
            // Update usage count if different
            if ($tag->usage_count !== $count) {
                $tag->usage_count = $count;
                $tag->save();
            }
        }
    }
    
    /**
     * Find tasks by tag name.
     */
    public function findTasksByTagName(string $tagName, Request $request = null): JsonResponse
    {
        $userId = Auth::id();
        
        // Try to find the tag
        $tag = \App\Models\Tag::where('name', $tagName)
            ->forUser($userId)
            ->first();
            
        if (!$tag) {
            return $this->successResponse([]);
        }
        
        // Build query for tasks with this tag
        $query = $tag->tasks()
            ->where('user_id', $userId);
            
        // Apply filters if request provided
        if ($request) {
            // Filter by completion status
            if ($request->has('completed')) {
                $completed = filter_var($request->completed, FILTER_VALIDATE_BOOLEAN);
                $query = $completed ? $query->where('completed', true) : $query->where('completed', false);
            }
            
            // Filter by due date
            if ($request->has('due_date')) {
                $query->whereDate('due_date', $request->due_date);
            }
            
            // Filter by priority
            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }
            
            // Order by
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDir = $request->get('sort_dir', 'desc');
            $query->orderBy($sortBy, $sortDir);
        }
        
        // Get tasks
        $tasks = $query->get();
        
        return $this->successResponse([
            'tag' => $tag,
            'tasks' => $tasks,
        ]);
    }
}
