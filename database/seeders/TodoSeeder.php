<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user (or create one if none exists)
        $user = User::first() ?? User::factory()->create();
        
        // Get categories for this user
        $categories = Category::where('user_id', $user->id)->get();
        
        // If no categories exist, create a default one
        if ($categories->isEmpty()) {
            $defaultCategory = Category::create([
                'name' => 'General',
                'color' => '#6366f1',
                'icon' => 'list',
                'user_id' => $user->id
            ]);
            $categories = collect([$defaultCategory]);
        }
        
        // Skip if tasks already exist for this user
        if (Task::where('user_id', $user->id)->exists()) {
            return;
        }
        
        // Sample tasks with different categories
        $tasks = [
            [
                'title' => 'Learn Laravel',
                'description' => 'Study the latest Laravel documentation and build a sample project',
                'due_date' => Carbon::now()->addDays(7),
                'priority' => 2, // High
                'completed' => false,
                'category_id' => $categories->random()->id,
                'progress' => 25,
                'tags' => ['development', 'learning']
            ],
            [
                'title' => 'Build a Task Manager',
                'description' => 'Create a task management application with Laravel and Livewire',
                'due_date' => Carbon::now()->addDays(14),
                'priority' => 1, // Medium
                'completed' => false,
                'category_id' => $categories->random()->id,
                'progress' => 50,
                'tags' => ['project', 'development']
            ],
            [
                'title' => 'Write Tests',
                'description' => 'Add unit and feature tests for the application',
                'due_date' => Carbon::now()->addDays(5),
                'priority' => 2, // High
                'completed' => false,
                'category_id' => $categories->random()->id,
                'progress' => 10,
                'tags' => ['testing', 'quality']
            ],
            [
                'title' => 'Setup CI/CD',
                'description' => 'Configure continuous integration and deployment for the project',
                'due_date' => Carbon::now()->addDays(21),
                'priority' => 0, // Low
                'completed' => false,
                'category_id' => $categories->random()->id,
                'progress' => 0,
                'tags' => ['devops', 'deployment']
            ],
            [
                'title' => 'Deploy to Production',
                'description' => 'Deploy the application to a production server',
                'due_date' => Carbon::now()->addDays(30),
                'priority' => 1, // Medium
                'completed' => false,
                'category_id' => $categories->random()->id,
                'progress' => 0,
                'reminder_at' => Carbon::now()->addDays(28)
            ],
            [
                'title' => 'Completed Task Example',
                'description' => 'This is an example of a completed task',
                'due_date' => Carbon::now()->subDays(1),
                'priority' => 0, // Low
                'completed' => true,
                'completed_at' => Carbon::now(),
                'category_id' => $categories->random()->id,
                'progress' => 100
            ],
        ];
        
        // Create the tasks
        foreach ($tasks as $task) {
            Task::create(array_merge($task, ['user_id' => $user->id]));
        }
    }
}
