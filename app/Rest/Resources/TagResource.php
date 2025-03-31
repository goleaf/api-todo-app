<?php

namespace App\Rest\Resources;

use App\Rest\Resource as RestResource;

class TagResource extends RestResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    public static $model = \App\Models\Tag::class;

    /**
     * The exposed fields that could be provided
     * @param \Lomkit\Rest\Http\Requests\RestRequest $request
     * @return array
     */
    public function fields(\Lomkit\Rest\Http\Requests\RestRequest $request): array
    {
        return [
            'id',
            'name',
            'color',
            'user_id',
            'usage_count',
            'created_at',
            'updated_at'
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
            'tasks' => new \Lomkit\Rest\Relations\Relation('tasks', \Lomkit\Rest\Http\Relations\BelongsToMany::class)
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
            'orderByUsage',
            'orderByName',
            'search',
            'nameLike'
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
        return true;
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
