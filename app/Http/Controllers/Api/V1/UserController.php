<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UpdatePasswordRequest;
use App\Http\Requests\Api\User\UpdatePhotoRequest;
use App\Http\Requests\Api\User\UpdateProfileRequest;
use App\Models\User;
use App\Traits\ApiResponses;
use App\Traits\LogsErrors;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UserController extends Controller
{
    use ApiResponses, LogsErrors;

    /**
     * Display the authenticated user.
     */
    public function show(?User $user = null): JsonResponse
    {
        try {
            // If no user is provided or the user is requesting their own profile
            if (! $user || $user->id === Auth::id()) {
                $user = Auth::user();
            } else {
                // Only allow access to other profiles if authorized
                $this->authorize('view', $user);
            }

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User profile retrieved successfully',
            ]);
        } catch (Throwable $e) {
            $this->logError($e);

            return $this->serverErrorResponse('Failed to retrieve user profile');
        }
    }

    /**
     * List all users (admin function)
     */
    public function index(): JsonResponse
    {
        try {
            $this->authorize('viewAny', User::class);
            $users = User::all();

            return $this->successResponse(
                data: $users,
                message: 'Users retrieved successfully'
            );
        } catch (Throwable $e) {
            $this->logError($e);

            return $this->serverErrorResponse('Failed to retrieve users');
        }
    }

    /**
     * Update the specified user's profile.
     * Maps to /api/users/{user} endpoint for PUT requests
     */
    public function update(UpdateProfileRequest $request, User $user): JsonResponse
    {
        try {
            // Check if user is updating their own profile or is authorized to update others
            if ($user->id !== Auth::id()) {
                // Return 403 Forbidden explicitly without going through the authorize method
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to update this profile',
                ], 403);
            }

            $validated = $request->validated();
            $user->update($validated);

            return response()->json([
                'status' => 'success',
                'data' => $user,
                'message' => 'Profile updated successfully',
            ], 200);
        } catch (Throwable $e) {
            $this->logError($e, ['request' => $request->all()]);

            return $this->serverErrorResponse('Failed to update profile');
        }
    }

    /**
     * Update the user's profile information.
     * Maps to /api/users/profile endpoint
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $validated = $request->validated();

            $user->update($validated);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Profile updated successfully',
            ]);
        } catch (Throwable $e) {
            $this->logError($e, ['request' => $request->all()]);

            return $this->serverErrorResponse('Failed to update profile');
        }
    }

    /**
     * Update the specified user's password.
     * Maps to /api/users/{user}/password endpoint for PUT requests
     * Also used for /api/users/password endpoint
     */
    public function updatePassword(UpdatePasswordRequest $request, ?User $user = null): JsonResponse
    {
        try {
            // If no user is provided, update the authenticated user's password
            if (! $user) {
                $user = Auth::user();
            } elseif ($user->id !== Auth::id()) {
                // Only allow updating own password
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not authorized to update this user\'s password',
                ], 403);
            }

            $validated = $request->validated();

            // Check current password
            if (! Hash::check($validated['current_password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The current password is incorrect',
                    'errors' => [
                        'current_password' => ['The provided password does not match our records.'],
                    ],
                ], 422);
            }

            $user->password = Hash::make($validated['password']);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully',
            ]);
        } catch (Throwable $e) {
            $this->logError($e);

            return $this->serverErrorResponse('Failed to update password');
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

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Profile photo uploaded successfully',
            ]);
        } catch (Throwable $e) {
            $this->logError($e);

            return $this->serverErrorResponse('Failed to upload profile photo');
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

            return response()->json([
                'success' => true,
                'message' => 'Profile photo deleted successfully',
            ]);
        } catch (Throwable $e) {
            $this->logError($e);

            return $this->serverErrorResponse('Failed to delete profile photo');
        }
    }

    /**
     * Get user statistics.
     */
    public function statistics(): JsonResponse
    {
        try {
            $user = Auth::user();

            $totalTasks = $user->tasks()->count();
            $completedTasks = $user->tasks()->where('completed', true)->count();
            $incompleteTasks = $user->tasks()->where('completed', false)->count();
            $totalCategories = $user->categories()->count();

            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'incomplete_tasks' => $incompleteTasks,
                    'completion_rate' => $completionRate,
                    'total_categories' => $totalCategories,
                ],
                'message' => 'User statistics retrieved successfully',
            ]);
        } catch (Throwable $e) {
            $this->logError($e);

            return $this->serverErrorResponse('Failed to retrieve user statistics');
        }
    }
}
