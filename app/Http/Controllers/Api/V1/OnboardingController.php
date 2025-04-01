<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Onboarding data retrieved successfully',
            'data' => [
                'steps' => [
                    [
                        'id' => 'welcome',
                        'title' => 'Welcome to the App',
                        'description' => 'Get started with our application'
                    ],
                    [
                        'id' => 'profile',
                        'title' => 'Setup Your Profile',
                        'description' => 'Complete your profile information'
                    ],
                    [
                        'id' => 'tasks',
                        'title' => 'Create Your First Task',
                        'description' => 'Learn how to create and manage tasks'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Skip the onboarding process.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function skip(Request $request): JsonResponse
    {
        // Mark the user as having completed onboarding
        $user = $request->user();
        $user->has_completed_onboarding = true;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Onboarding process skipped successfully'
        ]);
    }
} 