<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // No services to register
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register admin authentication extensions
        Auth::viaRequest('admin', function ($request) {
            if ($request->session()->has('admin_id')) {
                return Admin::find($request->session()->get('admin_id'));
            }
            return null;
        });
    }
}
