<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProfileDeleteRequest;
use App\Http\Requests\Api\ProfileUpdateRequest;
use App\Http\Requests\Api\User\UpdatePasswordRequest;
use App\Http\Requests\Api\User\UpdatePhotoRequest;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ApiResponses;

    /**
     * Get the authenticated user's profile information.
     */
    public function show(): JsonResponse
    {
        try {
            return $this->successResponse(
                data: Auth::user(),
                message: 'Profile retrieved successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error retrieving profile: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return $this->serverErrorResponse('Error retrieving profile');
        }
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $validated = $request->validated();

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            return $this->successResponse(
                data: $user,
                message: 'Profile updated successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error updating profile: '.$e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);

            return $this->serverErrorResponse('Error updating profile');
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(ProfileDeleteRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();

            // Revoke all tokens for this user
            $user->tokens()->delete();

            // Logout the user
            Auth::guard('web')->logout();

            // Delete the user
            $user->delete();

            return $this->successResponse(
                message: 'Account deleted successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error deleting account: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return $this->serverErrorResponse('Error deleting account');
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $validated = $request->validated();

            // Check current password
            if (! Hash::check($validated['current_password'], $user->password)) {
                return $this->errorResponse(
                    message: 'The current password is incorrect',
                    statusCode: 422,
                    errors: [
                        'current_password' => ['The provided password does not match our records.'],
                    ]
                );
            }

            $user->password = Hash::make($validated['password']);
            $user->save();

            return $this->successResponse(
                message: 'Password updated successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error updating password: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return $this->serverErrorResponse('Error updating password');
        }
    }

    /**
     * Upload a profile photo.
     */
    public function uploadPhoto(UpdatePhotoRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $validated = $request->validated();

            // Delete old photo if exists
            if ($user->photo_path && Storage::disk('public')->exists($user->photo_path)) {
                Storage::disk('public')->delete($user->photo_path);
            }

            // Store new photo
            $path = $request->file('photo')->store('profile-photos', 'public');

            $user->photo_path = $path;
            $user->save();

            return $this->successResponse(
                data: $user,
                message: 'Profile photo uploaded successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error uploading profile photo: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return $this->serverErrorResponse('Error uploading profile photo');
        }
    }

    /**
     * Delete the user's profile photo.
     */
    public function deletePhoto(): JsonResponse
    {
        try {
            $user = Auth::user();

            if ($user->photo_path) {
                if (Storage::disk('public')->exists($user->photo_path)) {
                    Storage::disk('public')->delete($user->photo_path);
                }

                $user->photo_path = null;
                $user->save();
            }

            return $this->successResponse(
                message: 'Profile photo deleted successfully'
            );
        } catch (\Exception $e) {
            Log::error('Error deleting profile photo: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return $this->serverErrorResponse('Error deleting profile photo');
        }
    }
}
