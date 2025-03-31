<?php

namespace App\Providers;

use App\Livewire\TaskBulkProcessor;
use App\Livewire\TaskMvc;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(HypervelServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Livewire components
        Livewire::component('task-bulk-processor', TaskBulkProcessor::class);
        Livewire::component('task-mvc', TaskMvc::class);
    }
}
