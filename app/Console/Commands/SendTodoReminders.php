<?php

namespace App\Console\Commands;

use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendTodoReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'todos:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for todos that are due';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->info('Checking for reminders at '.$now->toDateTimeString());

        // Find todos with reminders due in the last minute
        $todos = Todo::with('user')
            ->where('completed', false)
            ->where('reminder_at', '<=', $now)
            ->where('reminder_at', '>=', $now->copy()->subMinutes(1))
            ->get();

        $this->info("Found {$todos->count()} todos with reminders due.");

        foreach ($todos as $todo) {
            $this->info("Sending reminder for todo: {$todo->title} to user {$todo->user->name}");

            // In a real app, you would send an email or push notification here
            // For testing purposes, we'll just log it
            Log::info("Reminder: Todo '{$todo->title}' is due ".
                     ($todo->due_date ? 'on '.$todo->due_date->format('Y-m-d') : 'soon').
                     ' for user '.$todo->user->email);

            // For push notifications, you would integrate with Firebase or another service here

            // Mark reminder as sent by setting reminder_at to null
            // Alternative approach: add a reminder_sent boolean column
            $todo->reminder_at = null;
            $todo->save();
        }

        $this->info('Finished sending reminders.');

        return Command::SUCCESS;
    }
}
