<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Services\Api\AuthService;
use Illuminate\Http\JsonResponse;

class AuthApiController extends ApiController
{
    protected AuthService $service;

    /**
     * AuthApiController constructor.
     */
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->service->register($request->validated());
    }

    /**
     * Log in a user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->service->login($request->validated());
    }

    /**
     * Log out a user.
     */
    public function logout(): JsonResponse
    {
        return $this->service->logout();
    }

    /**
     * Refresh the user's token.
     */
    public function refresh(): JsonResponse
    {
        return $this->service->refresh();
    }

    /**
     * Get the authenticated user.
     */
    public function me(): JsonResponse
    {
        return $this->service->me();
    }
}
