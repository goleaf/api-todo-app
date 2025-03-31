<?php

namespace App\Providers;

use App\Console\Commands\GenerateModelScopes;
use Illuminate\Support\ServiceProvider;

class ScopesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->commands([
            GenerateModelScopes::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
} 