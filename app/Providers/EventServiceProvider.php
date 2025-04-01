<?php

namespace App\Providers;

use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use App\Events\CategoryCreated;
use App\Events\CategoryUpdated;
use App\Events\CategoryDeleted;
use App\Events\TaskCompleted;
use App\Events\TagCreated;
use App\Events\TagUpdated;
use App\Events\TagDeleted;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        TaskCreated::class => [],
        TaskUpdated::class => [],
        TaskDeleted::class => [],
        TaskCompleted::class => [],
        CategoryCreated::class => [],
        CategoryUpdated::class => [],
        CategoryDeleted::class => [],
        TagCreated::class => [],
        TagUpdated::class => [],
        TagDeleted::class => [],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
