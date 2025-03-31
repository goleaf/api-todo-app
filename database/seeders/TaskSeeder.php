<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            // Get user's categories
            $categories = Category::where('user_id', $user->id)->get();
            
            if ($categories->isEmpty()) {
                continue; // Skip users without categories
            }

            // Create predefined tasks with varied properties
            $this->createPredefinedTasksForUser($user, $categories);
            
            // Create some additional random tasks
            $this->createRandomTasksForUser($user, $categories);
        }
    }
    
    /**
     * Create predefined tasks for a user
     */
    private function createPredefinedTasksForUser($user, $categories)
    {
        // Get category IDs for easier use
        $categoryIds = $categories->pluck('id')->toArray();
        
        // Create a set of predefined tasks
        $tasks = [
            [
                'title' => 'Finalize project proposal',
                'description' => 'Complete the draft of the project proposal including budget and timeline',
                'due_date' => Carbon::now()->addDays(3),
                'priority' => 2, // High
                'completed' => false,
                'category_id' => $this->getCategoryByName($categories, 'Work'),
                'tags' => ['project', 'important', 'deadline'],
                'progress' => 75,
            ],
            [
                'title' => 'Schedule dentist appointment',
                'description' => 'Call dentist office for annual checkup',
                'due_date' => Carbon::now()->addDays(7),
                'priority' => 1, // Medium
                'completed' => false,
                'category_id' => $this->getCategoryByName($categories, 'Health'),
                'tags' => ['health', 'routine'],
                'reminder_at' => Carbon::now()->addDays(6)->setHour(9)->setMinute(0),
                'progress' => 0,
            ],
            [
                'title' => 'Submit expense report',
                'description' => 'Upload receipts and complete expense form for business trip',
                'due_date' => Carbon::now()->addDay(),
                'priority' => 2, // High
                'completed' => false,
                'category_id' => $this->getCategoryByName($categories, 'Work'),
                'tags' => ['finance', 'reports', 'urgent'],
                'progress' => 50,
            ],
            [
                'title' => 'Complete online course module',
                'description' => 'Finish Module 3 of the Advanced Programming course',
                'due_date' => Carbon::now()->addDays(5),
                'priority' => 1, // Medium
                'completed' => false,
                'category_id' => $this->getCategoryByName($categories, 'Education'),
                'tags' => ['learning', 'programming'],
                'progress' => 25,
            ],
            [
                'title' => 'Pay utilities bill',
                'description' => 'Pay water and electricity bill for the month',
                'due_date' => Carbon::now()->addDays(2),
                'priority' => 2, // High
                'completed' => false,
                'category_id' => $this->getCategoryByName($categories, 'Finance'),
                'reminder_at' => Carbon::now()->addDays(1)->setHour(15)->setMinute(0),
                'tags' => ['bills', 'monthly'],
                'progress' => 0,
            ],
            [
                'title' => 'Completed Task Example',
                'description' => 'This is an example of a completed task',
                'due_date' => Carbon::now()->subDays(1),
                'priority' => 0, // Low
                'completed' => true,
                'completed_at' => Carbon::now()->subHours(5),
                'category_id' => $this->getCategoryByName($categories, 'Personal'),
                'tags' => ['example', 'complete'],
                'progress' => 100,
            ],
        ];
        
        // Create the tasks
        foreach ($tasks as $task) {
            Task::create(array_merge($task, ['user_id' => $user->id]));
        }
    }
    
    /**
     * Create random tasks for a user
     */
    private function createRandomTasksForUser($user, $categories)
    {
        $priorities = [0, 1, 2]; // Low, Medium, High
        $categoryIds = $categories->pluck('id')->toArray();
        
        // Create 3-6 random tasks
        $count = rand(3, 6);
        for ($i = 0; $i < $count; $i++) {
            $completed = rand(0, 1) === 1;
            $progress = $completed ? 100 : rand(0, 90);
            
            // Create task with random properties
            Task::create([
                'user_id' => $user->id,
                'title' => 'Task ' . ($i + 1) . ' - ' . $this->getRandomTaskTitle(),
                'description' => $this->getRandomTaskDescription(),
                'due_date' => Carbon::now()->addDays(rand(-5, 14)),
                'priority' => $priorities[array_rand($priorities)],
                'completed' => $completed,
                'completed_at' => $completed ? Carbon::now()->subHours(rand(1, 100)) : null,
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'tags' => $this->getRandomTags(),
                'progress' => $progress,
                'reminder_at' => rand(0, 1) === 1 ? Carbon::now()->addDays(rand(1, 5)) : null,
            ]);
        }
    }
    
    /**
     * Get a category ID by name
     */
    private function getCategoryByName($categories, $name)
    {
        $category = $categories->where('name', $name)->first();
        return $category ? $category->id : $categories->first()->id;
    }
    
    /**
     * Get a random task title
     */
    private function getRandomTaskTitle()
    {
        $titles = [
            'Review document',
            'Prepare presentation',
            'Call client',
            'Research new tools',
            'Update website',
            'Plan meeting',
            'Order supplies',
            'Send email follow-up',
            'Organize files',
            'Clean workspace',
        ];
        
        return $titles[array_rand($titles)];
    }
    
    /**
     * Get a random task description
     */
    private function getRandomTaskDescription()
    {
        $descriptions = [
            'This task needs to be completed soon',
            'Make sure to check all the details before submitting',
            'Follow up with the team for feedback',
            'Requires focused concentration',
            'Should take about 30 minutes to complete',
            'An important task that will help project progress',
            'Remember to document all steps taken',
            'Coordinate with relevant team members',
            'Check resources before starting',
            'May need approval from management',
        ];
        
        return $descriptions[array_rand($descriptions)];
    }
    
    /**
     * Get random tags for a task
     */
    private function getRandomTags()
    {
        $allTags = [
            'urgent', 'important', 'routine', 'follow-up', 'meeting',
            'client', 'internal', 'planning', 'review', 'documentation',
            'research', 'development', 'design', 'testing', 'deployment',
            'admin', 'personal', 'team', 'project', 'quarterly'
        ];
        
        // Get 0-3 random tags
        $tagCount = rand(0, 3);
        if ($tagCount === 0) {
            return [];
        }
        
        // Shuffle and take the first $tagCount elements
        shuffle($allTags);
        return array_slice($allTags, 0, $tagCount);
    }
}
