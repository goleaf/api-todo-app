<?php

namespace App\Services\Api;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    use ApiResponse;

    /**
     * Register a new user.
     */
    public function register(array $validatedData): JsonResponse
    {
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->createdResponse([
            'user' => $user,
            'token' => $token,
        ], __('messages.auth.registered'));
    }

    /**
     * Log in a user.
     */
    public function login(array $validatedData): JsonResponse
    {
        if (!Auth::attempt($validatedData)) {
            return $this->errorResponse(
                __('messages.auth.invalid_credentials'),
                401
            );
        }

        $user = User::where('email', $validatedData['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
        ], __('messages.auth.login_success'));
    }

    /**
     * Log out a user.
     */
    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return $this->successResponse(null, __('messages.auth.logout_success'));
    }

    /**
     * Refresh the user's token.
     */
    public function refresh(): JsonResponse
    {
        $user = auth()->user();
        
        // Delete existing tokens
        $user->tokens()->delete();
        
        // Create a new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
        ], __('messages.auth.token_refreshed'));
    }

    /**
     * Get the authenticated user.
     */
    public function me(): JsonResponse
    {
        $user = auth()->user();
        
        $user->load(['tasks' => function ($query) {
            $query->latest()->limit(5);
        }, 'categories']);
        
        return $this->successResponse($user);
    }
} 