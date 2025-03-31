<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UserPasswordUpdateRequest;
use App\Http\Requests\Api\User\UserPhotoUploadRequest;
use App\Http\Requests\Api\User\UserProfileUpdateRequest;
use App\Services\Api\UserService;
use Illuminate\Http\JsonResponse;

class ProfileApiController extends Controller
{
    protected UserService $service;

    /**
     * ProfileApiController constructor.
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Get the authenticated user's profile.
     */
    public function show(): JsonResponse
    {
        return $this->service->getProfile();
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UserProfileUpdateRequest $request): JsonResponse
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
    public function uploadPhoto(UserPhotoUploadRequest $request): JsonResponse
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
} 