<?php

namespace App\Providers;

use App\Services\UserSettingsService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class FrontendServiceProvider extends ServiceProvider
{
    /**
     * Register any frontend services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(UserSettingsService::class, function ($app) {
            return new UserSettingsService();
        });
        
        // Register a shorter alias for the service
        $this->app->alias(UserSettingsService::class, 'user.settings');
    }

    /**
     * Bootstrap any frontend services.
     *
     * @return void
     */
    public function boot()
    {
        // Share user settings with all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $settingsService = app(UserSettingsService::class);
                
                // Share settings with view
                $view->with('userTheme', $settingsService->get('theme', 'light'));
                $view->with('userPrimaryColor', $settingsService->get('primary_color', '#4f46e5'));
            }
        });
    }
} 