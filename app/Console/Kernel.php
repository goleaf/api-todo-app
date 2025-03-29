<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run the todo reminders check every minute
        $schedule->command('todos:send-reminders')->everyMinute();

        // Refresh database with seeds (useful for demo environments)
        // $schedule->command('migrate:fresh --seed')->dailyAt('01:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');

        // Custom command to refresh only seed data without migrating
        Artisan::command('db:refresh-seeds', function () {
            $this->info('Refreshing all seed data...');
            $this->call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
            $this->info('All seed data has been refreshed!');
        })->purpose('Refresh all seed data without running migrations');
    }
}
