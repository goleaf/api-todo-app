<?php

namespace App\Services\Api;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryService extends ApiService
{
    /**
     * CategoryService constructor.
     */
    public function __construct(Category $model)
    {
        $this->model = $model;
        $this->defaultRelations = ['tasks'];
    }

    /**
     * Override the buildIndexQuery method to apply user filtering.
     */
    protected function buildIndexQuery(Request $request): Builder
    {
        $query = parent::buildIndexQuery($request);
        
        // Only show categories belonging to the authenticated user
        $query->where('user_id', auth()->id());
        
        return $query;
    }

    /**
     * Override the store method to add the user_id.
     */
    public function store(array $validatedData): JsonResponse
    {
        // Add the authenticated user ID
        $validatedData['user_id'] = auth()->id();
        
        return parent::store($validatedData);
    }

    /**
     * Get the number of tasks in each category.
     */
    public function getTaskCounts(): JsonResponse
    {
        $userId = auth()->id();
        
        $categories = $this->model->where('user_id', $userId)
            ->withCount(['tasks', 'tasks as completed_tasks_count' => function ($query) {
                $query->where('completed', true);
            }])
            ->get();
        
        return $this->successResponse($categories);
    }

    /**
     * Get the allowed relations for this service.
     */
    protected function getAllowedRelations(): array
    {
        return ['tasks', 'user'];
    }
} 