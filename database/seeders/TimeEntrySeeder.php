<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TimeEntry;
use App\Models\Task;
use App\Models\User;

class TimeEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $tasks = Task::all();

        $users->each(function ($user) use ($tasks) {
            $userTasks = $tasks->where('user_id', $user->id);
            
            $userTasks->each(function ($task) {
                $startTime = now()->subDays(rand(1, 30))->setTime(rand(8, 17), rand(0, 59));
                $endTime = $startTime->copy()->addMinutes(rand(15, 480));
                
                TimeEntry::create([
                    'user_id' => $task->user_id,
                    'task_id' => $task->id,
                    'started_at' => $startTime,
                    'ended_at' => $endTime,
                    'description' => 'Sample time entry for task: ' . $task->title,
                ]);
            });
        });
    }
} 