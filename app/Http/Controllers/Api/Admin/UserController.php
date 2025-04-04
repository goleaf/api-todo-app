<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends BaseController
{
    /**
     * Display a listing of users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::where('is_admin', false)
            ->withCount('tasks')
            ->latest()
            ->paginate(10);

        return $this->successResponse($users);
    }

    /**
     * Store a newly created user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => false,
        ]);

        return $this->successResponse($user, 'User created successfully');
    }

    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        if ($user->is_admin) {
            return $this->notFoundResponse();
        }

        $user->load(['tasks' => function ($query) {
            $query->latest()->take(5);
        }]);

        return $this->successResponse($user);
    }

    /**
     * Update the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        if ($user->is_admin) {
            return $this->notFoundResponse();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'is_active' => 'boolean',
        ]);

        $user->update($validated);

        return $this->successResponse($user, 'User updated successfully');
    }

    /**
     * Remove the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        if ($user->is_admin) {
            return $this->notFoundResponse();
        }

        $user->delete();

        return $this->successResponse(null, 'User deleted successfully');
    }

    /**
     * Update user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request, User $user)
    {
        if ($user->is_admin) {
            return $this->notFoundResponse();
        }

        $validated = $request->validate([
            'password' => ['required', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->successResponse(null, 'Password updated successfully');
    }
} 