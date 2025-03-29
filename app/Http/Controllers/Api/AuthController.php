<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }

    /**
     * Login an existing user.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        
        if (!Auth::attempt($validated)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
        
        $user = User::where('email', $validated['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    /**
     * Logout the current user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Refresh the user's token.
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        
        // Revoke the current token
        $user->currentAccessToken()->delete();
        
        // Create a new token
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'data' => [
                'token' => $token
            ]
        ]);
    }
} 