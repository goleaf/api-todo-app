<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        // Create predefined todos for the first user (admin)
        $adminUser = User::where('email', 'admin@example.com')->first();

        if ($adminUser) {
            $todos = [
                [
                    'user_id' => $adminUser->id,
                    'title' => 'Learn Laravel',
                    'description' => 'Study the latest Laravel documentation and build a sample project',
                    'completed' => false,
                    'due_date' => now()->addDays(7),
                    'priority' => 2, // High
                ],
                [
                    'user_id' => $adminUser->id,
                    'title' => 'Create a Todo App',
                    'description' => 'Develop a simple Todo application with Laravel and Livewire',
                    'completed' => false,
                    'due_date' => now()->addDays(14),
                    'priority' => 1, // Medium
                ],
                [
                    'user_id' => $adminUser->id,
                    'title' => 'Take a break',
                    'description' => 'Remember to take breaks and rest your eyes from the screen',
                    'completed' => true,
                    'due_date' => now()->subDays(1),
                    'priority' => 0, // Low
                ],
            ];

            foreach ($todos as $todo) {
                Todo::create($todo);
            }
        }

        // Create random todos for each user
        foreach ($users as $user) {
            // Create 3-7 todos for each user using factory
            Todo::factory()
                ->count(rand(3, 7))
                ->create(['user_id' => $user->id]);
        }
    }
}
