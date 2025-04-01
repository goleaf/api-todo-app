<?php

namespace App\Rest\Controllers;

use App\Rest\Controller as RestController;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoriesController extends RestController
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = \App\Rest\Resources\CategoryResource::class;
    
    /**
     * Get task counts for all categories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function taskCounts()
    {
        $userId = auth()->id();
        
        $categories = Category::where('user_id', $userId)
            ->withCount(['tasks', 'completedTasks', 'incompleteTasks'])
            ->get()
            ->map(function ($category) {
                $category->completion_percentage = $category->tasks_count > 0 
                    ? round(($category->completed_tasks_count / $category->tasks_count) * 100, 2) 
                    : 0;
                return $category;
            });
            
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
