<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Policies\CategoryPolicy;
use App\Policies\TagPolicy;
use App\Policies\TaskPolicy;
use App\Policies\TimeEntryPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Task::class => TaskPolicy::class,
        Category::class => CategoryPolicy::class,
        Tag::class => TagPolicy::class,
        TimeEntry::class => TimeEntryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->configureGates();
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiters()
    {
        //
    }

    /**
     * Configure the permissions that users can assign to other users.
     *
     * @return void
     */
    protected function configureGates()
    {
        // Define any custom gates here
    }
} 