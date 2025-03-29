<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display the authenticated user.
     */
    public function show()
    {
        $user = Auth::user();
        return response()->json(['data' => $user]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes', 
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users')->ignore($user->id)
            ],
        ]);
        
        $user->update($validated);
        
        return response()->json(['data' => $user]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'The current password is incorrect.',
                'errors' => [
                    'current_password' => ['The provided password does not match our records.']
                ]
            ], 422);
        }
        
        $user->password = Hash::make($validated['password']);
        $user->save();
        
        return response()->json(['message' => 'Password updated successfully']);
    }

    /**
     * Upload a profile photo.
     */
    public function uploadPhoto(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Delete old photo if exists
        if ($user->photo_path && Storage::disk('public')->exists($user->photo_path)) {
            Storage::disk('public')->delete($user->photo_path);
        }
        
        // Store new photo
        $path = $request->file('photo')->store('profile-photos', 'public');
        
        $user->photo_path = $path;
        $user->save();
        
        return response()->json(['data' => $user]);
    }

    /**
     * Delete the user's profile photo.
     */
    public function deletePhoto()
    {
        $user = Auth::user();
        
        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
            $user->photo_path = null;
            $user->save();
        }
        
        return response()->json(['message' => 'Photo deleted successfully']);
    }

    /**
     * Get user statistics.
     */
    public function statistics()
    {
        $user = Auth::user();
        
        $totalTasks = $user->tasks()->count();
        $completedTasks = $user->tasks()->completed()->count();
        $incompleteTasks = $user->tasks()->incomplete()->count();
        $totalCategories = $user->categories()->count();
        
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        
        return response()->json([
            'data' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'incomplete_tasks' => $incompleteTasks,
                'completion_rate' => $completionRate,
                'total_categories' => $totalCategories
            ]
        ]);
    }
} 