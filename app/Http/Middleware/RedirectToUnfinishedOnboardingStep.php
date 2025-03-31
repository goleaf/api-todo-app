<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectToUnfinishedOnboardingStep
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip if not authenticated or requesting an API endpoint
        if (!Auth::check() || $request->is('api/*')) {
            return $next($request);
        }

        // Skip if exempt routes
        $exemptRoutes = [
            'onboarding.*',
            'profile.*',
            'logout',
            'api.*'
        ];
        foreach ($exemptRoutes as $route) {
            if ($request->routeIs($route)) {
                return $next($request);
            }
        }

        $user = Auth::user();
        
        // If onboarding is in progress, redirect to the next unfinished step
        if (!$user->onboarding()->inProgress()){
        
        return $next($request);
    } 
            $nextStep = $user->onboarding()->nextUnfinishedStep();
            
            // Only redirect if the current path is not already the next step's link
            if ($request->path() !== ltrim($nextStep->link, '/')) {
                return redirect()->to($nextStep->link);
            }
        
        
        return $next($request);
    }
} 