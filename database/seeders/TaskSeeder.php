<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create random tasks for each user
        User::all()->each(function (User $user) {
            // Get all categories for this user
            $categories = Category::where('user_id', $user->id)->get();
            $tags = Tag::where('user_id', $user->id)->get();
            
            // Create between 15-30 tasks per user
            $taskCount = fake()->numberBetween(15, 30);
            
            for ($i = 0; $i < $taskCount; $i++) {
                // Determine if task is completed
                $completed = fake()->boolean(30); // 30% chance of being completed
                
                // Create random due date (past, today, future)
                $dueOption = fake()->randomElement(['past', 'today', 'future', null]);
                $dueDate = null;
                
                if ($dueOption === 'past') {
                    $dueDate = Carbon::now()->subDays(fake()->numberBetween(1, 14));
                } elseif ($dueOption === 'today') {
                    $dueDate = Carbon::today();
                } elseif ($dueOption === 'future') {
                    $dueDate = Carbon::now()->addDays(fake()->numberBetween(1, 30));
                }
                
                // Randomly select a category
                $category = $categories->random();
                
                // Create task
                $task = Task::create([
                    'title' => fake()->sentence(fake()->numberBetween(3, 8)),
                    'description' => fake()->boolean(70) ? fake()->paragraph() : null,
                    'due_date' => $dueDate,
                    'priority' => fake()->randomElement(TaskPriority::cases()),
                    'completed' => $completed,
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'notes' => fake()->boolean(30) ? fake()->paragraphs(2, true) : null,
                    'progress' => $completed ? 100 : fake()->numberBetween(0, 90),
                    'completed_at' => $completed ? Carbon::now()->subDays(fake()->numberBetween(0, 5)) : null,
                ]);
                
                // Attach random tags (0-3)
                $tagCount = fake()->numberBetween(0, 3);
                if ($tagCount > 0 && $tags->count() > 0) {
                    $randomTags = $tags->random(min($tagCount, $tags->count()))->pluck('id')->toArray();
                    $task->tags()->attach($randomTags);
                }
            }
        });
    }
}
