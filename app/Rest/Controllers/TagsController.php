<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class TagsController extends RestController
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = \App\Rest\Resources\TagResource::class;
    
    /**
     * Get task counts for all tags
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function taskCounts()
    {
        $userId = auth()->id();
        
        $tags = Tag::where('user_id', $userId)
            ->withCount('tasks')
            ->orderBy('tasks_count', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $tags
        ]);
    }
    
    /**
     * Merge tags into a single tag
     *
     * @param array $parameters
     * @return \Illuminate\Http\JsonResponse
     */
    public function merge($parameters = [])
    {
        $source = Tag::findOrFail($parameters['source_id'] ?? 0);
        $target = Tag::findOrFail($parameters['target_id'] ?? 0);
        
        if ($source->id === $target->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot merge a tag with itself',
                'errors' => ['source_id' => 'Source and target tags must be different']
            ], 422);
        }
        
        // Move tasks from source to target
        $tasks = $source->tasks;
        foreach ($tasks as $task) {
            // Check if the task already has the target tag
            if (!$task->tags()->where('tags.id', $target->id)->exists()) {
                $task->tags()->attach($target->id);
            }
            $task->tags()->detach($source->id);
        }
        
        // Update target tag's usage count
        $target->usage_count += $source->usage_count;
        $target->save();
        
        // Delete the source tag
        $source->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Tags merged successfully',
            'data' => $target
        ]);
    }
    
    /**
     * Get tag suggestions based on input
     *
     * @param array $parameters
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestions($parameters = [])
    {
        $userId = auth()->id();
        $query = $parameters['query'] ?? '';
        
        $tags = Tag::where('user_id', $userId)
            ->where('name', 'like', "%{$query}%")
            ->orderBy('usage_count', 'desc')
            ->limit(10)
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $tags
        ]);
    }
    
    /**
     * Create multiple tags at once
     *
     * @param array $parameters
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchCreate($parameters = [])
    {
        $userId = auth()->id();
        $tagNames = $parameters['names'] ?? [];
        $createdTags = [];
        
        foreach ($tagNames as $name) {
            $tag = Tag::firstOrCreate(
                ['name' => $name, 'user_id' => $userId],
                ['color' => Tag::generateDefaultColor($name)]
            );
            
            $createdTags[] = $tag;
        }
        
        return response()->json([
            'success' => true,
            'message' => count($createdTags) . ' tags created or found',
            'data' => $createdTags
        ]);
    }
}
