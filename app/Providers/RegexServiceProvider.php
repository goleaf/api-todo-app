<?php

namespace App\Providers;

use App\Helpers\RegexHelper;
use Illuminate\Support\ServiceProvider;

class RegexServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('regex', function () {
            return new RegexHelper();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
} 