<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{
    /**
     * Login admin and create token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($validated)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $validated['email'])->firstOrFail();

        if (!$user->is_admin) {
            return $this->unauthorizedResponse('Unauthorized access');
        }

        $token = $user->createToken('admin_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
        ], 'Logged in successfully');
    }

    /**
     * Logout admin (Revoke the token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * Get the authenticated admin user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return $this->successResponse($request->user());
    }

    /**
     * Send password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !$user->is_admin) {
            return $this->errorResponse('Invalid email address');
        }

        $status = Password::sendResetLink($validated);

        if ($status === Password::RESET_LINK_SENT) {
            return $this->successResponse(null, 'Password reset link sent successfully');
        }

        return $this->errorResponse('Unable to send password reset link');
    }

    /**
     * Reset password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !$user->is_admin) {
            return $this->errorResponse('Invalid email address');
        }

        $status = Password::reset($validated, function ($user) {
            $user->forceFill([
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ])->save();
        });

        if ($status === Password::PASSWORD_RESET) {
            return $this->successResponse(null, 'Password reset successfully');
        }

        return $this->errorResponse('Unable to reset password');
    }
} 