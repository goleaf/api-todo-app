<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class OnboardingController extends Controller
{
    /**
     * Show the onboarding dashboard.
     */
    public function index(Request $request)
    {
        return view('onboarding.index');
    }

    /**
     * Skip the onboarding process.
     */
    public function skip(Request $request): RedirectResponse
    {
        // We don't actually "skip" the onboarding in a technical sense,
        // but we can redirect the user to the dashboard to avoid the middleware
        return redirect()->route('dashboard');
    }
} 