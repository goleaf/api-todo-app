<?php

namespace App\Services\Api;

use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class ApiService
{
    use ApiResponse;

    /**
     * The model instance.
     */
    protected Model $model;

    /**
     * The default relations to load with the model.
     */
    protected array $defaultRelations = [];

    /**
     * The default columns to select.
     */
    protected array $defaultSelect = ['*'];

    /**
     * Get all resources with optional filtering and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $this->buildIndexQuery($request);
        
        // Apply pagination if needed
        if ($request->has('per_page')) {
            $perPage = (int) $request->get('per_page', 15);
            $data = $query->paginate($perPage);
        } else {
            $data = $query->get();
        }

        return $this->successResponse($data);
    }

    /**
     * Build the query for the index method.
     */
    protected function buildIndexQuery(Request $request): Builder
    {
        $query = $this->model->newQuery();
        
        // Apply relations if requested
        if ($request->has('with')) {
            $relations = explode(',', $request->get('with'));
            $validRelations = array_intersect($relations, $this->getAllowedRelations());
            $query->with($validRelations);
        } elseif (!empty($this->defaultRelations)) {
            $query->with($this->defaultRelations);
        }
        
        // Apply column selection
        if ($request->has('select')) {
            $columns = explode(',', $request->get('select'));
            $query->select($columns);
        } else {
            $query->select($this->defaultSelect);
        }
        
        // Apply search if provided
        if ($request->has('search') && $request->has('search_fields')) {
            $search = $request->get('search');
            $searchFields = explode(',', $request->get('search_fields'));
            
            $query->where(function($q) use ($search, $searchFields) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$search}%");
                }
            });
        }
        
        // Apply sorting
        if ($request->has('sort_by')) {
            $sortField = $request->get('sort_by');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortField, $sortDirection);
        }
        
        return $query;
    }

    /**
     * Get a specific resource.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $query = $this->model->newQuery();
        
        // Apply relations if requested
        if ($request->has('with')) {
            $relations = explode(',', $request->get('with'));
            $validRelations = array_intersect($relations, $this->getAllowedRelations());
            $query->with($validRelations);
        } elseif (!empty($this->defaultRelations)) {
            $query->with($this->defaultRelations);
        }
        
        $model = $query->find($id);
        
        if (!$model) {
            return $this->notFoundResponse(__('messages.not_found'));
        }
        
        return $this->successResponse($model);
    }

    /**
     * Store a new resource.
     */
    public function store(array $validatedData): JsonResponse
    {
        $model = $this->model->create($validatedData);
        
        if (!empty($this->defaultRelations)) {
            $model->load($this->defaultRelations);
        }
        
        return $this->createdResponse($model);
    }

    /**
     * Update an existing resource.
     */
    public function update(int $id, array $validatedData): JsonResponse
    {
        $model = $this->model->find($id);
        
        if (!$model) {
            return $this->notFoundResponse(__('messages.not_found'));
        }
        
        $model->update($validatedData);
        
        if (!empty($this->defaultRelations)) {
            $model->load($this->defaultRelations);
        }
        
        return $this->successResponse($model);
    }

    /**
     * Delete a resource.
     */
    public function destroy(int $id): JsonResponse
    {
        $model = $this->model->find($id);
        
        if (!$model) {
            return $this->notFoundResponse(__('messages.not_found'));
        }
        
        $model->delete();
        
        return $this->noContentResponse();
    }

    /**
     * Get the allowed relations for this service.
     */
    protected function getAllowedRelations(): array
    {
        return $this->defaultRelations;
    }
} 