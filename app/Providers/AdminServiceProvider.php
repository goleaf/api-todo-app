<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Task;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Register any admin services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any admin services.
     *
     * @return void
     */
    public function boot()
    {
        // Share admin dashboard data with admin views
        View::composer('admin.*', function ($view) {
            $view->with('adminStats', $this->getAdminStats());
        });
    }
    
    /**
     * Get admin dashboard quick stats.
     *
     * @return array
     */
    protected function getAdminStats()
    {
        // Only calculate stats when the user is authenticated and is an admin
        if (!Auth::check() || !Auth::user()->is_admin) {
            return [];
        }
        
        return [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::where('completed', true)->count(),
        ];
    }
} 