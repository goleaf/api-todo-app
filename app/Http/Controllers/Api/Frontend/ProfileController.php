<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends BaseController
{
    /**
     * Display the user's profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        return $this->successResponse(Auth::user());
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($validated);

        return $this->successResponse($user, 'Profile updated successfully');
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return $this->successResponse(null, 'Password updated successfully');
    }

    /**
     * Delete the user's account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required', 'current_password'],
            'confirmation' => ['required', 'string', 'in:DELETE'],
        ]);

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        return $this->successResponse(null, 'Account deleted successfully');
    }
} 