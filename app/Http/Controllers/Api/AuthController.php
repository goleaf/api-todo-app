<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register a new user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            // Check if email already exists (double check)
            if (User::where('email', $validated['email'])->exists()) {
                return $this->validationErrorResponse([
                    'email' => ['This email is already registered.']
                ]);
            }

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Create token
            $tokenName = $request->device_name ?? ($request->input('device_name') ?? 'auth_token');
            $token = $user->createToken($tokenName)->plainTextToken;

            return $this->successResponse(
                data: [
                    'user' => $user,
                    'token' => $token
                ],
                message: 'Registration successful',
                code: 201
            );
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors
            Log::error('Registration database error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            // Check for duplicate entry error
            if ($e->getCode() == 23000) { // Integrity constraint violation
                return $this->validationErrorResponse([
                    'email' => ['This email is already registered.']
                ]);
            }
            
            return $this->serverErrorResponse('An error occurred during registration. Please try again.');
        } catch (\Exception $e) {
            // Log detailed error
            Log::error('Registration error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            
            return $this->serverErrorResponse('An error occurred during registration. Please try again.');
        }
    }

    /**
     * Authenticate user and issue token
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
                return $this->unauthorizedResponse('The provided credentials are incorrect.');
            }

            $user = User::where('email', $validated['email'])->first();

            if ($request->has('device_name')) {
                $token = $user->createToken($request->device_name)->plainTextToken;
            } else {
                $token = $user->createToken('auth_token')->plainTextToken;
            }

            return $this->successResponse(
                data: [
                    'user' => $user,
                    'token' => $token
                ],
                message: 'Login successful'
            );
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all()
            ]);
            return $this->serverErrorResponse('An error occurred while logging in. Please try again.');
        }
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->successResponse(message: 'Successfully logged out');
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return $this->serverErrorResponse('An error occurred while logging out');
        }
    }

    /**
     * Refresh the user's token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Revoke the current token
            $user->currentAccessToken()->delete();
            
            // Create a new token
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return $this->successResponse(
                data: [
                    'token' => $token
                ],
                message: 'Token refreshed successfully'
            );
        } catch (\Exception $e) {
            Log::error('Token refresh error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return $this->serverErrorResponse('An error occurred while refreshing your token');
        }
    }

    /**
     * Get the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        try {
            return $this->successResponse(
                data: [
                    'user' => $request->user()
                ]
            );
        } catch (\Exception $e) {
            Log::error('Get authenticated user error: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return $this->serverErrorResponse('An error occurred while fetching user data');
        }
    }
} 