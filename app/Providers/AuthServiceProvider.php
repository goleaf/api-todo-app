<?php

namespace App\Providers;

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
        \App\Models\Task::class => \App\Policies\TaskPolicy::class,
        \App\Models\TimeEntry::class => \App\Policies\TimeEntryPolicy::class,
        \App\Models\Attachment::class => \App\Policies\AttachmentPolicy::class,
        \App\Models\Tag::class => \App\Policies\TagPolicy::class,
        \App\Models\SmartTag::class => \App\Policies\SmartTagPolicy::class,
        \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
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
        Gate::define('access-admin', function ($user) {
            return true; // Allow all authenticated users to access admin
            // For role-based access, you would check roles here
            // return $user->hasRole('admin');
        });
    }
} 