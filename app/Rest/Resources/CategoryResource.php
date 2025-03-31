<?php

namespace App\Rest\Resources;

use App\Rest\Resource as RestResource;

class CategoryResource extends RestResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    public static $model = \App\Models\Category::class;

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
            'description',
            'color',
            'icon',
            'type',
            'user_id',
            'created_at',
            'updated_at',
            'task_count',
            'completed_task_count',
            'completion_percentage'
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
            'user' => \App\Rest\Resources\UserResource::class,
            'tasks' => \App\Rest\Resources\TaskResource::class,
            'completedTasks' => \App\Rest\Resources\TaskResource::class,
            'incompleteTasks' => \App\Rest\Resources\TaskResource::class,
            'taskTags' => \App\Rest\Resources\TagResource::class,
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
            'orderByName',
            'withTasks',
            'withIncompleteTasks',
            'withTag',
            'search',
            'withTaskCounts'
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
