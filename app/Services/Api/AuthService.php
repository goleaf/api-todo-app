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
    public function register(array $data): JsonResponse
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->createdResponse([
            'user' => $user,
            'token' => $token,
        ], __('messages.auth.registered'));
    }

    /**
     * Login a user and issue a token.
     */
    public function login(array $credentials): JsonResponse
    {
        if (! Auth::attempt($credentials)) {
            return $this->errorResponse(__('validation.auth.invalid_credentials'), 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
        ], __('messages.auth.logged_in'));
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return $this->successResponse([], __('messages.auth.logged_out'));
    }

    /**
     * Refresh the user's token.
     */
    public function refresh(): JsonResponse
    {
        $user = Auth::user();
        $user->tokens()->delete();
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
        $user = Auth::user();

        return $this->successResponse($user);
    }
}
