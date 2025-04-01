<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Seeder;

class TimeEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            // Get user's tasks
            $tasks = Task::where('user_id', $user->id)->get();

            // Create completed time entries for random tasks
            $tasks->random(10)->each(function ($task) {
                // Create 1-3 time entries for each task
                for ($i = 0; $i < rand(1, 3); $i++) {
                    $startDate = now()->subDays(rand(1, 30));
                    $duration = rand(15, 120); // Duration in minutes

                    TimeEntry::create([
                        'task_id' => $task->id,
                        'start_time' => $startDate,
                        'end_time' => $startDate->copy()->addMinutes($duration),
                        'duration' => $duration,
                    ]);
                }
            });

            // Create some ongoing time entries
            $tasks->random(2)->each(function ($task) {
                TimeEntry::create([
                    'task_id' => $task->id,
                    'start_time' => now()->subMinutes(rand(5, 60)),
                    'end_time' => null,
                    'duration' => null,
                ]);
            });
        });
    }
} 