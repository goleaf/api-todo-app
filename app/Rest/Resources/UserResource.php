<?php

namespace App\Rest\Resources;

use App\Rest\Resource as RestResource;

class UserResource extends RestResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    public static $model = \App\Models\User::class;

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
            'email',
            'photo_path',
            'role',
            'active',
            'photo_url',
            'created_at',
            'updated_at',
            'email_verified_at'
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
            'tasks' => \App\Rest\Resources\TaskResource::class,
            'categories' => \App\Rest\Resources\CategoryResource::class,
            'tags' => \App\Rest\Resources\TagResource::class,
            'completedTasks' => \App\Rest\Resources\TaskResource::class,
            'incompleteTasks' => \App\Rest\Resources\TaskResource::class,
            'tasksDueToday' => \App\Rest\Resources\TaskResource::class,
            'overdueTasks' => \App\Rest\Resources\TaskResource::class,
            'upcomingTasks' => \App\Rest\Resources\TaskResource::class,
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
            'search',
            'withRole'
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
}
