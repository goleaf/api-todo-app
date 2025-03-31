<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AdminAuthenticate;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Register admin guard
        Auth::resolved(function ($auth) {
            $auth->extend('admin', function () {
                return Auth::createSessionDriver('admin');
            });
        });

        $this->app->singleton('admin', function ($app) {
            return new AdminAuthenticate();
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Enable web routes for admin panel
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
            
            // Admin routes
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        });
    }
}
