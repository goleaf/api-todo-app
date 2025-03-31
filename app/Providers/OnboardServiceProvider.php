<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Spatie\Onboard\Facades\Onboard;

class OnboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Step 1: Complete Profile
        Onboard::addStep('Complete Profile')
            ->link('/profile')
            ->cta('Complete')
            ->completeIf(function (User $model) {
                // Consider a profile complete if they have uploaded a photo
                return $model->photo_path !== null;
            });

        // Step 2: Create First Task
        Onboard::addStep('Create Your First Task')
            ->link('/api/tasks')
            ->cta('Create Task')
            ->completeIf(function (User $model) {
                return $model->tasks()->count() > 0;
            });

        // Step 3: Create a Category
        Onboard::addStep('Create a Category')
            ->link('/api/categories')
            ->cta('Create Category')
            ->completeIf(function (User $model) {
                return $model->categories()->count() > 0;
            });

        // Step 4: Add a Tag to a Task
        Onboard::addStep('Add a Tag to a Task')
            ->link('/api/tags')
            ->cta('Add Tag')
            ->completeIf(function (User $model) {
                return $model->tags()->count() > 0;
            });

        // Step 5: Complete a Task
        Onboard::addStep('Complete a Task')
            ->link('/tasks')
            ->cta('Complete Task')
            ->completeIf(function (User $model) {
                return $model->completedTasks()->count() > 0;
            });
    }
} 