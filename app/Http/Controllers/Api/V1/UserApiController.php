<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UserProfileUpdateRequest;
use App\Http\Requests\Api\User\UserPasswordUpdateRequest;
use App\Services\Api\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserApiController extends Controller
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
     * Display the specified resource.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        return $this->service->show($id, $request);
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