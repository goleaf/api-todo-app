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
    public const HOME = '/admin/dashboard';

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

        // Register admin middleware directly in the route middleware
        $this->app->singleton('admin.auth', function ($app) {
            return new AdminAuthenticate();
        });

        $this->app->singleton('admin', function ($app) {
            return new AdminAuthenticate();
        });

        // Register custom route middleware
        $this->app['router']->aliasMiddleware('admin.api', \App\Http\Middleware\AdminApi::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Admin routes
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        });
    }
}
