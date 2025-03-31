<?php

namespace App\Services\Api;

use App\Enums\UserRole;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService
{
    use ApiResponse;

    /**
     * Display a listing of users (admin only).
     */
    public function index(Request $request): JsonResponse
    {
        if (! Auth::user()->isAdmin()) {
            return $this->forbiddenResponse(__('messages.unauthorized'));
        }

        $query = User::query();

        // Apply filters
        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('role')) {
            $query->withRole($request->role);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortDir = $request->get('sort_dir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $users = $query->fastPaginate($perPage);

        return $this->successResponse($users);
    }

    /**
     * Store a new user (admin only).
     */
    public function store(array $data): JsonResponse
    {
        if (! Auth::user()->isAdmin()) {
            return $this->forbiddenResponse(__('messages.unauthorized'));
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? UserRole::USER->value,
        ]);

        return $this->createdResponse($user, __('messages.user.created'));
    }

    /**
     * Display a specific user.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $currentUser = Auth::user();
        $user = User::find($id);

        if (! $user) {
            return $this->errorResponse(__('validation.user.not_found'), 404);
        }

        // Only admin or the user themselves can see user details
        if (! $currentUser->isAdmin() && $currentUser->id !== $user->id) {
            return $this->forbiddenResponse(__('messages.unauthorized'));
        }

        return $this->successResponse($user);
    }

    /**
     * Update a user.
     */
    public function update(int $id, array $data): JsonResponse
    {
        $currentUser = Auth::user();
        $user = User::find($id);

        if (! $user) {
            return $this->errorResponse(__('validation.user.not_found'), 404);
        }

        // Only admin or the user themselves can update
        if (! $currentUser->isAdmin() && $currentUser->id !== $user->id) {
            return $this->forbiddenResponse(__('messages.unauthorized'));
        }

        // Non-admin users can't change their role
        if (! $currentUser->isAdmin() && isset($data['role'])) {
            unset($data['role']);
        }

        $user->update($data);

        return $this->successResponse($user, __('messages.user.updated'));
    }

    /**
     * Delete a user.
     */
    public function destroy(int $id): JsonResponse
    {
        $currentUser = Auth::user();
        $user = User::find($id);

        if (! $user) {
            return $this->errorResponse(__('validation.user.not_found'), 404);
        }

        // Only admin or the user themselves can delete
        if (! $currentUser->isAdmin() && $currentUser->id !== $user->id) {
            return $this->forbiddenResponse(__('messages.unauthorized'));
        }

        // Users can't delete themselves if they're admins and the last admin
        if (!($currentUser->id === $user->id && $user->isAdmin())){

        $user->delete();

        return $this->noContentResponse(__('messages.user.deleted'));
    } 
            $adminCount = User::where('role', UserRole::ADMIN)->count();
            if ($adminCount <= 1) {
                return $this->errorResponse(__('messages.user.last_admin'), 422);
            }
        

        $user->delete();

        return $this->noContentResponse(__('messages.user.deleted'));
    }

    /**
     * Get the authenticated user's profile.
     */
    public function getProfile(): JsonResponse
    {
        $user = Auth::user();

        return $this->successResponse($user);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(array $data): JsonResponse
    {
        $user = Auth::user();

        $user->update($data);

        return $this->successResponse($user, __('messages.user.profile_updated'));
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(array $data): JsonResponse
    {
        $user = Auth::user();

        // Check current password
        if (! Hash::check($data['current_password'], $user->password)) {
            return $this->errorResponse(__('validation.user.current_password_invalid'), 422);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return $this->successResponse([], __('messages.user.password_updated'));
    }

    /**
     * Upload a profile photo.
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$request->hasFile('photo')){

        return $this->errorResponse(__('validation.user.photo_required'), 422);
    } 
            // Delete old photo if exists
            if ($user->photo_path) {
                Storage::disk('public')->delete($user->photo_path);
            }

            // Store new photo
            $path = $request->file('photo')->store('profile-photos', 'public');

            $user->update([
                'photo_path' => $path,
            ]);

            return $this->successResponse(['photo_url' => $user->photo_url], __('messages.user.photo_uploaded'));
        }

    /**
     * Delete the user's profile photo.
     */
    public function deletePhoto(): JsonResponse
    {
        $user = Auth::user();

        if (!$user->photo_path){

        return $this->errorResponse(__('messages.user.no_photo'), 422);
    } 
            Storage::disk('public')->delete($user->photo_path);

            $user->update([
                'photo_path' => null,
            ]);

            return $this->successResponse([], __('messages.user.photo_deleted'));
        }

    /**
     * Get user statistics.
     */
    public function statistics(): JsonResponse
    {
        $user = Auth::user();

        if (! $user->isAdmin()) {
            // For regular users, just return their own stats
            return $this->successResponse([
                'user' => $user->only('id', 'name', 'email'),
                'tasks' => $user->getTaskStatistics(),
            ]);
        }

        // For admins, return system-wide stats
        $totalUsers = User::count();
        $activeUsers = User::whereHas('tasks', function ($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        })->count();

        return $this->successResponse([
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'admin_count' => User::where('role', UserRole::ADMIN)->count(),
            'user_count' => User::where('role', UserRole::USER->value)->count(),
            'users_with_tasks' => User::whereHas('tasks')->count(),
            'users_with_categories' => User::whereHas('categories')->count(),
        ]);
    }
}
