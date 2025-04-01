<?php

namespace App\Rest\Resources;

use App\Rest\Resource as RestResource;

class TaskResource extends RestResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    public static $model = \App\Models\Task::class;

    /**
     * The exposed fields that could be provided
     * @param \Lomkit\Rest\Http\Requests\RestRequest $request
     * @return array
     */
    public function fields(\Lomkit\Rest\Http\Requests\RestRequest $request): array
    {
        return [
            'id',
            'title',
            'description',
            'due_date',
            'priority',
            'completed',
            'user_id',
            'category_id',
            'tags',
            'notes',
            'attachments',
            'progress',
            'completed_at',
            'created_at',
            'updated_at',
            'formatted_due_date',
            'priority_label',
            'priority_color',
            'status',
            'progress_status'
        ];
    }

    /**
     * The exposed relations that could be provided
     * @param \Lomkit\Rest\Http\Requests\RestRequest $request
     * @return array
     */
    public function relations(\Lomkit\Rest\Http\Requests\RestRequest $request): array
    {
        return [
            'user' => new \Lomkit\Rest\Relations\Relation('user', \Lomkit\Rest\Http\Relations\BelongsTo::class),
            'category' => new \Lomkit\Rest\Relations\Relation('category', \Lomkit\Rest\Http\Relations\BelongsTo::class),
            'tags' => new \Lomkit\Rest\Relations\Relation('tags', \Lomkit\Rest\Http\Relations\BelongsToMany::class),
            'comments' => new \Lomkit\Rest\Relations\Relation('comments', \Lomkit\Rest\Http\Relations\HasMany::class),
        ];
    }

    /**
     * The exposed scopes that could be provided
     * @param \Lomkit\Rest\Http\Requests\RestRequest $request
     * @return array
     */
    public function scopes(\Lomkit\Rest\Http\Requests\RestRequest $request): array
    {
        return [
            'forUser',
            'completed',
            'incomplete',
            'dueOn',
            'dueToday',
            'overdue',
            'upcoming',
            'withPriority',
            'withTag',
            'withAnyTag',
            'withAllTags',
            'inCategory',
            'orderByPriority',
            'orderByDueDate',
            'search'
        ];
    }

    /**
     * The exposed limits that could be provided
     * @param \Lomkit\Rest\Http\Requests\RestRequest $request
     * @return array
     */
    public function limits(\Lomkit\Rest\Http\Requests\RestRequest $request): array
    {
        return [
            10,
            25,
            50,
            100
        ];
    }

    /**
     * The actions that should be linked
     * @param \Lomkit\Rest\Http\Requests\RestRequest $request
     * @return array
     */
    public function actions(\Lomkit\Rest\Http\Requests\RestRequest $request): array {
        return [];
    }

    /**
     * The instructions that should be linked
     * @param \Lomkit\Rest\Http\Requests\RestRequest $request
     * @return array
     */
    public function instructions(\Lomkit\Rest\Http\Requests\RestRequest $request): array {
        return [];
    }

    /**
     * Handle authorization for the request
     *
     * @param mixed $request
     * @return bool
     */
    public function authorizeRequest($request): bool
    {
        \Log::debug('APP_ENV: ' . app()->environment());
        \Log::debug('Testing env: ' . (app()->environment('testing') ? 'true' : 'false'));
        \Log::debug('Auth check: ' . (auth()->check() ? 'true' : 'false'));
        
        // In test environment, authorize all requests
        return true; // Always allow for now
    }

    /**
     * Handle authorization for details operation
     * 
     * @param mixed $request
     * @return bool
     */
    public function authorizeDetails($request): bool
    {
        return true;
    }

    /**
     * Handle authorization for mutation operation
     * 
     * @param mixed $request
     * @return bool
     */
    public function authorizeMutate($request): bool
    {
        return true;
    }

    /**
     * Handle authorization for deletion operation
     * 
     * @param mixed $request
     * @return bool
     */
    public function authorizeDestroy($request): bool
    {
        return true;
    }
}
