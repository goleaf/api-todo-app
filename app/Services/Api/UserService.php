<?php

namespace App\Services\Api;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserService extends ApiService
{
    /**
     * UserService constructor.
     */
    public function __construct(User $model)
    {
        $this->model = $model;
        $this->defaultRelations = ['tasks', 'categories'];
    }

    /**
     * Override the buildIndexQuery method to restrict access.
     */
    protected function buildIndexQuery(Request $request): Builder
    {
        $query = parent::buildIndexQuery($request);
        
        // Only admins should see all users, regular users just see themselves
        if (!auth()->user()->isAdmin()) {
            $query->where('id', auth()->id());
        }
        
        return $query;
    }

    /**
     * Get the authenticated user's profile.
     */
    public function getProfile(): JsonResponse
    {
        $user = auth()->user();
        
        if (!empty($this->defaultRelations)) {
            $user->load($this->defaultRelations);
        }
        
        return $this->successResponse($user);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(array $validatedData): JsonResponse
    {
        $user = auth()->user();
        $user->update($validatedData);
        
        if (!empty($this->defaultRelations)) {
            $user->load($this->defaultRelations);
        }
        
        return $this->successResponse($user, __('messages.user.profile_updated'));
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(array $validatedData): JsonResponse
    {
        $user = auth()->user();
        
        $user->password = Hash::make($validatedData['password']);
        $user->save();
        
        return $this->successResponse(null, __('messages.user.password_updated'));
    }

    /**
     * Upload a profile photo.
     */
    public function uploadPhoto(Request $request): JsonResponse
    {
        $user = auth()->user();
        
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            // Delete previous photo if exists
            if ($user->photo_path && Storage::disk('public')->exists($user->photo_path)) {
                Storage::disk('public')->delete($user->photo_path);
            }
            
            // Store new photo
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->photo_path = $path;
            $user->save();
            
            return $this->successResponse(['photo_url' => Storage::url($path)], __('messages.user.photo_uploaded'));
        }
        
        return $this->errorResponse('No valid photo was uploaded.', 422);
    }

    /**
     * Delete the user's profile photo.
     */
    public function deletePhoto(): JsonResponse
    {
        $user = auth()->user();
        
        if ($user->photo_path && Storage::disk('public')->exists($user->photo_path)) {
            Storage::disk('public')->delete($user->photo_path);
            $user->photo_path = null;
            $user->save();
            
            return $this->successResponse(null, __('messages.user.photo_deleted'));
        }
        
        return $this->notFoundResponse('No profile photo found.');
    }

    /**
     * Get the user's task statistics.
     */
    public function getStatistics(): JsonResponse
    {
        $userId = auth()->id();
        
        $taskStats = app(TaskService::class)->getStatistics();
        $categoryStats = app(CategoryService::class)->getTaskCounts();
        
        // Combine the statistics
        $stats = [
            'tasks' => $taskStats->original['data'],
            'categories' => $categoryStats->original['data'],
        ];
        
        return $this->successResponse($stats);
    }

    /**
     * Get the allowed relations for this service.
     */
    protected function getAllowedRelations(): array
    {
        return ['tasks', 'categories'];
    }
} 