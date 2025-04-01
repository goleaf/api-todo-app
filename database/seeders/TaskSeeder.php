<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            // Get user's categories and tags
            $categories = Category::where('user_id', $user->id)->get();
            $tags = Tag::where('user_id', $user->id)->get();

            // Create tasks with random categories and tags
            Task::factory(15)->create([
                'user_id' => $user->id,
                'category_id' => function () use ($categories) {
                    return $categories->random()->id;
                },
            ])->each(function ($task) use ($tags) {
                // Attach random number of tags (1-3) to each task
                $task->tags()->attach(
                    $tags->random(rand(1, 3))->pluck('id')->toArray()
                );
            });

            // Create some completed tasks
            Task::factory(5)->create([
                'user_id' => $user->id,
                'category_id' => function () use ($categories) {
                    return $categories->random()->id;
                },
                'completed_at' => now()->subDays(rand(1, 30)),
            ])->each(function ($task) use ($tags) {
                $task->tags()->attach(
                    $tags->random(rand(1, 3))->pluck('id')->toArray()
                );
            });

            // Create some overdue tasks
            Task::factory(3)->create([
                'user_id' => $user->id,
                'category_id' => function () use ($categories) {
                    return $categories->random()->id;
                },
                'due_date' => now()->subDays(rand(1, 10)),
            ])->each(function ($task) use ($tags) {
                $task->tags()->attach(
                    $tags->random(rand(1, 3))->pluck('id')->toArray()
                );
            });
        });
    }
} 