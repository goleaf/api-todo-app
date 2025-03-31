<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\TodoReminderNotification;
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
    protected $signature = 'todos:send-reminders {--due-today : Send reminders only for todos due today} {--overdue : Send reminders only for overdue todos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications for todos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to send todo reminders...');

        $dueToday = $this->option('due-today');
        $overdue = $this->option('overdue');

        $usersWithTodos = User::whereHas('tasks', function ($query) use ($dueToday, $overdue) {
            $query->where('completed', false);

            if ($dueToday) {
                $query->whereDate('due_date', Carbon::today());
            } elseif ($overdue) {
                $query->whereDate('due_date', '<', Carbon::today());
            } else {
                // Either due today, or with an active reminder
                $query->where(function ($subquery) {
                    $subquery->whereDate('due_date', Carbon::today())
                        ->orWhere(function ($remind) {
                            $remind->whereNotNull('reminder_at')
                                ->where('reminder_at', '<=', now());
                        });
                });
            }
        })->get();

        $count = 0;

        foreach ($usersWithTodos as $user) {
            $todos = $user->tasks()
                ->where('completed', false)
                ->where(function ($query) use ($dueToday, $overdue) {
                    if ($dueToday) {
                        $query->whereDate('due_date', Carbon::today());
                    } elseif ($overdue) {
                        $query->whereDate('due_date', '<', Carbon::today());
                    } else {
                        // Either due today, or with an active reminder
                        $query->whereDate('due_date', Carbon::today())
                            ->orWhere(function ($remind) {
                                $remind->whereNotNull('reminder_at')
                                    ->where('reminder_at', '<=', now());
                            });
                    }
                })
                ->get();

            foreach ($todos as $todo) {
                try {
                    $user->notify(new TodoReminderNotification($todo));
                    $count++;

                    // Reset the reminder time to avoid repeated notifications
                    if ($todo->reminder_at && $todo->reminder_at->isPast()) {
                        $todo->reminder_at = null;
                        $todo->save();
                    }

                    $this->info("Reminder sent for todo: {$todo->title} to {$user->email}");
                } catch (\Exception $e) {
                    Log::error("Error sending reminder for todo {$todo->id}: {$e->getMessage()}");
                    $this->error("Error sending reminder for todo {$todo->id}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Sent {$count} todo reminders");
    }
}
