<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    /**
     * Get the authenticated admin user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }
    
    /**
     * Authenticate an admin user and issue an API token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $admin = Admin::where('email', $request->email)->first();
        
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }
        
        // Create token with admin abilities
        $deviceName = $request->device_name ?? $request->userAgent() ?? 'Unknown Device';
        $token = $admin->createToken($deviceName, ['admin'])->plainTextToken;
        
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'admin' => $admin,
                'token' => $token
            ]
        ]);
    }
    
    /**
     * Logout the admin user (revoke the token).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }
    
    /**
     * Log out from all devices (revoke all tokens).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out from all devices'
        ]);
    }
} 