<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\User\UserPasswordUpdateRequest;
use App\Http\Requests\Api\User\UserProfileUpdateRequest;
use App\Http\Requests\Api\User\UserStoreRequest;
use App\Services\Api\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserApiController extends ApiController
{
    protected UserService $service;

    /**
     * UserApiController constructor.
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        return $this->service->index($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request): JsonResponse
    {
        return $this->service->store($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        return $this->service->show($id, $request);
    }

    /**
     * Update the specified resource.
     */
    public function update(UserProfileUpdateRequest $request, int $id): JsonResponse
    {
        return $this->service->update($id, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->service->destroy($id);
    }

    /**
     * Get the authenticated user's profile.
     */
    public function getProfile(): JsonResponse
    {
        return $this->service->getProfile();
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(UserProfileUpdateRequest $request): JsonResponse
    {
        return $this->service->updateProfile($request->validated());
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UserPasswordUpdateRequest $request): JsonResponse
    {
        return $this->service->updatePassword($request->validated());
    }

    /**
     * Upload a profile photo.
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        return $this->service->uploadPhoto($request);
    }

    /**
     * Delete the user's profile photo.
     */
    public function deletePhoto(): JsonResponse
    {
        return $this->service->deletePhoto();
    }

    /**
     * Get the user's statistics.
     */
    public function statistics(): JsonResponse
    {
        return $this->service->getStatistics();
    }
}
