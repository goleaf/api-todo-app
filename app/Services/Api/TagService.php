<?php

namespace App\Services\Api;

use App\Models\Tag;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TagService
{
    use ApiResponse;

    /**
     * Display a listing of tags with filtering capabilities.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = Tag::forUser($user->id);
        
        // Apply search filter
        if ($request->has('search')) {
            $query->search($request->search);
        }
        
        // Apply sorting
        if ($request->input('sort') === 'usage') {
            $query->orderByUsage();
        } else {
            $query->orderByName();
        }
        
        // For tests, if we're not paginating, just get all tags
        if ($request->has('no_pagination')) {
            $tags = $query->get();
            return $this->successResponse($tags);
        }
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $tags = $query->paginate($perPage);
        
        return $this->successResponse($tags->items());
    }

    /**
     * Store a new tag.
     */
    public function store(array $data): JsonResponse
    {
        $data['user_id'] = Auth::id();
        
        // Set default color if not provided
        if (!isset($data['color'])) {
            $data['color'] = '#6B7280';
        }
        
        $tag = Tag::create($data);
        
        return $this->createdResponse($tag, __('messages.tag.created'));
    }

    /**
     * Display a specific tag.
     */
    public function show(int $id): JsonResponse
    {
        $user = Auth::user();
        $tag = Tag::forUser($user->id)->find($id);
        
        if (!$tag) {
            return $this->errorResponse(__('validation.tag.not_found'), 404);
        }
        
        return $this->successResponse($tag);
    }

    /**
     * Update a tag.
     */
    public function update(int $id, array $data): JsonResponse
    {
        $user = Auth::user();
        $tag = Tag::forUser($user->id)->find($id);
        
        if (!$tag) {
            return $this->errorResponse(__('validation.tag.not_found'), 404);
        }
        
        $tag->update($data);
        
        return $this->successResponse($tag, __('messages.tag.updated'));
    }

    /**
     * Delete a tag.
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $tag = Tag::forUser($user->id)->find($id);
        
        if (!$tag) {
            return $this->errorResponse(__('validation.tag.not_found'), 404);
        }
        
        // Remove tag from tasks
        $tag->tasks()->detach();
        
        // Delete the tag
        $tag->delete();
        
        return $this->noContentResponse(__('messages.tag.deleted'));
    }

    /**
     * Get popular tags for the user.
     */
    public function popular(int $limit = 10): JsonResponse
    {
        $user = Auth::user();
        $tags = Tag::forUser($user->id)
            ->orderByUsage()
            ->limit($limit)
            ->get();
            
        return $this->successResponse($tags);
    }

    /**
     * Get all tasks with a specific tag.
     */
    public function getTasks(int $id): JsonResponse
    {
        $user = Auth::user();
        $tag = Tag::forUser($user->id)->find($id);
        
        if (!$tag) {
            return $this->errorResponse(__('validation.tag.not_found'), 404);
        }
        
        $tasks = $tag->tasks()->get();
        
        return $this->successResponse([
            'tag' => $tag,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Get task counts by tag.
     */
    public function getTaskCounts(): JsonResponse
    {
        $user = Auth::user();
        $tags = Tag::forUser($user->id)
            ->withCount(['tasks', 'tasks as completed_count' => function ($query) {
                $query->where('completed', true);
            }])
            ->orderByUsage()
            ->get()
            ->map(function ($tag) {
                $total = $tag->tasks_count;
                $completed = $tag->completed_count;
                $incomplete = $total - $completed;
                $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
                
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                    'usage_count' => $tag->usage_count,
                    'tasks_count' => $total,
                    'completed_count' => $completed,
                    'incomplete_count' => $incomplete,
                    'completion_rate' => $completionRate,
                ];
            });
            
        return $this->successResponse($tags);
    }

    /**
     * Merge two tags.
     * All tasks associated with the source tag will be associated with the target tag,
     * and the source tag will be deleted.
     */
    public function mergeTags(int $sourceTagId, int $targetTagId): JsonResponse
    {
        $user = Auth::user();
        
        // Get both tags and verify they exist and belong to the user
        $sourceTag = Tag::forUser($user->id)->find($sourceTagId);
        $targetTag = Tag::forUser($user->id)->find($targetTagId);
        
        if (!$sourceTag) {
            return $this->errorResponse(__('validation.tag.source_not_found'), 404);
        }
        
        if (!$targetTag) {
            return $this->errorResponse(__('validation.tag.target_not_found'), 404);
        }
        
        if ($sourceTagId === $targetTagId) {
            return $this->errorResponse(__('validation.tag.same_tag'), 422);
        }
        
        // Get all tasks associated with the source tag
        $tasks = $sourceTag->tasks()->get();
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // For each task, add the target tag if it's not already there
            foreach ($tasks as $task) {
                // Check if the task already has the target tag
                if (!$task->tags()->where('tag_id', $targetTag->id)->exists()) {
                    // Add the target tag to the task
                    $task->tags()->attach($targetTag->id);
                }
                
                // Remove the source tag from the task
                $task->tags()->detach($sourceTag->id);
            }
            
            // Delete the source tag
            $sourceTag->delete();
            
            // Update usage count for target tag
            $targetTag->usage_count = $targetTag->tasks()->count();
            $targetTag->save();
            
            DB::commit();
            
            return $this->successResponse(
                $targetTag,
                __('messages.tag.merged', [
                    'source' => $sourceTag->name, 
                    'target' => $targetTag->name
                ])
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(__('messages.tag.merge_failed'), 500);
        }
    }
} 