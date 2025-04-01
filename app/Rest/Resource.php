<?php

namespace App\Rest;

use Lomkit\Rest\Http\Resource as RestResource;

abstract class Resource extends RestResource
{
    /**
     * Build a "search" query for fetching resource.
     *
     * @param  \Lomkit\Rest\Http\Requests\RestRequest  $request
     * @param  \Illuminate\Contracts\Database\Eloquent\Builder  $query
     * @return \Illuminate\Contracts\Database\Eloquent\Builder
     */
    public function searchQuery(\Lomkit\Rest\Http\Requests\RestRequest $request, \Illuminate\Contracts\Database\Eloquent\Builder $query) {
        return $query;
    }

    /**
     * Build a query for mutating resource.
     *
     * @param  \Lomkit\Rest\Http\Requests\RestRequest  $request
     * @param  \Illuminate\Contracts\Database\Eloquent\Builder  $query
     * @return \Illuminate\Contracts\Database\Eloquent\Builder
     */
    public function mutateQuery(\Lomkit\Rest\Http\Requests\RestRequest $request, \Illuminate\Contracts\Database\Eloquent\Builder $query) {
        return $query;
    }

    /**
     * Build a "destroy" query for the given resource.
     *
     * @param  \Lomkit\Rest\Http\Requests\RestRequest  $request
     * @param  \Illuminate\Contracts\Database\Eloquent\Builder  $query
     * @return \Illuminate\Contracts\Database\Eloquent\Builder
     */
    public function destroyQuery(\Lomkit\Rest\Http\Requests\RestRequest $request, \Illuminate\Contracts\Database\Eloquent\Builder $query)
    {
        return $query;
    }

    /**
     * Build a "restore" query for the given resource.
     *
     * @param  \Lomkit\Rest\Http\Requests\RestRequest  $request
     * @param  \Illuminate\Contracts\Database\Eloquent\Builder  $query
     * @return \Illuminate\Contracts\Database\Eloquent\Builder
     */
    public function restoreQuery(\Lomkit\Rest\Http\Requests\RestRequest $request, \Illuminate\Contracts\Database\Eloquent\Builder $query)
    {
        return $query;
    }

    /**
     * Build a "forceDelete" query for the given resource.
     *
     * @param  \Lomkit\Rest\Http\Requests\RestRequest  $request
     * @param  \Illuminate\Contracts\Database\Eloquent\Builder  $query
     * @return \Illuminate\Contracts\Database\Eloquent\Builder
     */
    public function forceDeleteQuery(\Lomkit\Rest\Http\Requests\RestRequest $request, \Illuminate\Contracts\Database\Eloquent\Builder $query)
    {
        return $query;
    }

    /**
     * Base authorization method to determine if the user can perform the requested action
     *
     * @param mixed $request The request being authorized
     * @return bool
     */
    public function authorizeRequest($request): bool
    {
        // Always authorize in tests
        return true;
    }

    /**
     * Authorize viewing resources
     *
     * @param mixed $request The request being authorized
     * @return bool
     */
    public function authorizeDetails($request): bool
    {
        return true;
    }

    /**
     * Authorize searching resources
     *
     * @param mixed $request The request being authorized
     * @return bool
     */
    public function authorizeSearch($request): bool
    {
        return true;
    }

    /**
     * Authorize creating/updating resources
     *
     * @param mixed $request The request being authorized
     * @return bool
     */
    public function authorizeMutate($request): bool
    {
        return true;
    }

    /**
     * Authorize deleting resources
     *
     * @param mixed $request The request being authorized
     * @return bool
     */
    public function authorizeDestroy($request): bool
    {
        return true;
    }
}
