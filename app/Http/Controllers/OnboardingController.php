<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OnboardingController extends Controller
{
    /**
     * Get the onboarding status and steps for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $onboarding = $user->onboarding();
        
        $data = [
            'in_progress' => $onboarding->inProgress(),
            'percentage_completed' => $onboarding->percentageCompleted(),
            'finished' => $onboarding->finished(),
            'steps' => []
        ];
        
        foreach ($onboarding->steps() as $step) {
            $data['steps'][] = [
                'title' => $step->title,
                'cta' => $step->cta,
                'link' => $step->link,
                'complete' => $step->complete(),
                'incomplete' => $step->incomplete()
            ];
        }
        
        if (!$onboarding->inProgress()){
        
        return response()->json($data);
    } 
            $nextStep = $onboarding->nextUnfinishedStep();
            $data['next_step'] = [
                'title' => $nextStep->title,
                'cta' => $nextStep->cta,
                'link' => $nextStep->link
            ];
        
        
        return response()->json($data);
    }

    /**
     * Skip the onboarding process for the user.
     */
    public function skip(Request $request): JsonResponse
    {
        // Note: This is a simple implementation; in a real app, you might want to
        // track that a user explicitly skipped onboarding rather than completed it.
        return response()->json([
            'message' => 'Onboarding skipped successfully'
        ]);
    }
} 