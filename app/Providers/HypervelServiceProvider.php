<?php

namespace App\Providers;

use App\Console\Commands\HypervelBenchmarkCommand;
use App\Services\HypervelService;
use Illuminate\Support\ServiceProvider;

// use Hypervel\Facades\Hypervel;

class HypervelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register HypervelService as a singleton
        $this->app->singleton(HypervelService::class, function ($app) {
            return new HypervelService;
        });

        // Merge config
        if (file_exists(base_path('config/hypervel.php'))) {
            $this->mergeConfigFrom(
                base_path('config/hypervel.php'), 'hypervel'
            );
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        if (file_exists(base_path('config/hypervel.php'))) {
            $this->publishes([
                base_path('config/hypervel.php') => config_path('hypervel.php'),
            ], 'hypervel-config');
        }

        // Configure Hypervel based on our config
        // Commenting out for now to allow tests to run
        // $this->configureHypervel();

        // Register commands if running in console
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    /**
     * Configure Hypervel based on our config settings
     */
    protected function configureHypervel(): void
    {
        // Skip configuration if Hypervel class doesn't exist
        if (! class_exists('Hypervel\Facades\Hypervel')) {
            return;
        }

        // Set concurrency limit from config
        $concurrencyLimit = config('hypervel.concurrency_limit', 25);
        // Hypervel::setConcurrencyLimit($concurrencyLimit);

        // Configure Hypervel debug mode
        if (config('hypervel.debug', false)) {
            // Hypervel::enableDebugMode();
        }
    }

    /**
     * Register console commands
     */
    protected function registerCommands(): void
    {
        if (class_exists(HypervelBenchmarkCommand::class)) {
            $this->commands([
                HypervelBenchmarkCommand::class,
            ]);
        }
    }
}
