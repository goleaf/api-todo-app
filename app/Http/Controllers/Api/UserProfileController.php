<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserProfile\UpdateProfileRequest;
use App\Http\Requests\Api\UserProfile\UpdatePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $user = auth()->user();
        $user->load(['categories', 'tags']);

        // Get task statistics
        $taskStats = [
            'total_tasks' => $user->tasks()->count(),
            'completed_tasks' => $user->tasks()->where('status', 'completed')->count(),
            'overdue_tasks' => $user->tasks()
                ->where('status', '!=', 'completed')
                ->where('due_date', '<', now())
                ->count(),
            'due_today' => $user->tasks()
                ->where('status', '!=', 'completed')
                ->whereDate('due_date', today())
                ->count(),
        ];

        return response()->json([
            'data' => [
                'user' => $user,
                'statistics' => $taskStats,
                'preferences' => $user->preferences
            ]
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @param UpdateProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // Update user information
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->timezone = $request->timezone ?? $user->timezone;
        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Update the user's password.
     *
     * @param UpdatePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }

    /**
     * Update user preferences.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'preferences' => ['required', 'array'],
            'preferences.default_view' => ['nullable', 'string', 'in:list,board,calendar'],
            'preferences.task_reminder_time' => ['nullable', 'integer', 'min:0', 'max:168'],
            'preferences.daily_summary' => ['nullable', 'boolean'],
            'preferences.theme' => ['nullable', 'string', 'in:light,dark,system'],
            'preferences.week_starts_on' => ['nullable', 'integer', 'min:0', 'max:6'],
        ]);

        $user = auth()->user();
        $user->preferences = array_merge($user->preferences ?? [], $request->preferences);
        $user->save();

        return response()->json([
            'message' => 'Preferences updated successfully',
            'data' => $user->preferences
        ]);
    }

    /**
     * Delete the user's avatar.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAvatar()
    {
        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
            $user->save();
        }

        return response()->json([
            'message' => 'Avatar deleted successfully'
        ]);
    }

    /**
     * Get user's activity log.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activityLog(Request $request)
    {
        $query = auth()->user()->activities()
            ->with('subject')
            ->latest();

        // Filter by activity type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $activities = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $activities->items(),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total()
            ]
        ]);
    }
} 