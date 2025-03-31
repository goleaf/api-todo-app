<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;

class TestTaskCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-task-creation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test task CRUD operations to verify the migration from Todo to Task model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Testing Task Operations ===');
        
        // Get the first user
        $user = User::first();
        
        if (!$user) {
            $this->error('No user found. Please run migrations and seeders first.');
            return 1;
        }
        
        $this->info('Using user: ' . $user->name . ' (ID: ' . $user->id . ')');
        
        // Get a category
        $category = Category::where('user_id', $user->id)->first();
        
        if (!$category) {
            $this->info('Creating a default category for the user...');
            $category = Category::create([
                'name' => 'Test Category',
                'color' => '#4f46e5',
                'icon' => 'check',
                'user_id' => $user->id
            ]);
        }
        
        $this->info('Using category: ' . $category->name . ' (ID: ' . $category->id . ')');
        
        // 1. CREATE: Create a test task
        $this->info("\n1. CREATING a new task...");
        
        try {
            $task = Task::create([
                'title' => 'Test Task ' . now()->toDateTimeString(),
                'description' => 'This is a test task to verify the migration from Todo to Task model',
                'due_date' => now()->addDays(3),
                'category_id' => $category->id,
                'priority' => 1, // Medium
                'user_id' => $user->id,
                'completed' => false,
                'progress' => 0,
                'tags' => ['test', 'verification']
            ]);
            
            $this->info('Task created successfully with ID: ' . $task->id);
            
            // 2. READ: Read the task back
            $this->info("\n2. READING the created task...");
            $savedTask = Task::find($task->id);
            
            if (!$savedTask) {
                $this->error('Failed to read task with ID: ' . $task->id);
                return 1;
            }
            
            $this->table(
                ['ID', 'Title', 'Priority', 'Category', 'Due Date'], 
                [[
                    $savedTask->id,
                    $savedTask->title,
                    $savedTask->priority_label,
                    $category->name,
                    $savedTask->due_date->format('Y-m-d')
                ]]
            );
            
            // 3. UPDATE: Update the task
            $this->info("\n3. UPDATING the task...");
            $savedTask->title = 'Updated: ' . $savedTask->title;
            $savedTask->priority = 2; // High
            $savedTask->progress = 50;
            $savedTask->save();
            
            $this->info('Task updated successfully.');
            
            $updatedTask = Task::find($task->id);
            $this->table(
                ['ID', 'Title', 'Priority', 'Progress', 'Category'], 
                [[
                    $updatedTask->id,
                    $updatedTask->title,
                    $updatedTask->priority_label,
                    $updatedTask->progress . '%',
                    $category->name
                ]]
            );
            
            // 4. LIST: List all tasks for the user
            $this->info("\n4. LISTING all tasks for the user...");
            $allTasks = Task::where('user_id', $user->id)->get();
            
            $taskData = $allTasks->map(function($task) {
                return [
                    $task->id,
                    $task->title,
                    $task->priority_label,
                    $task->completed ? 'Yes' : 'No',
                    $task->due_date ? $task->due_date->format('Y-m-d') : 'None'
                ];
            })->toArray();
            
            $this->table(
                ['ID', 'Title', 'Priority', 'Completed', 'Due Date'], 
                $taskData
            );
            
            $this->info("Found " . count($allTasks) . " tasks for user ID: " . $user->id);
            
            // 5. DELETE: Delete the test task
            $this->info("\n5. DELETING the test task...");
            $result = $updatedTask->delete();
            
            if ($result) {
                $this->info("Task with ID: {$updatedTask->id} deleted successfully.");
            } else {
                $this->error("Failed to delete task with ID: {$updatedTask->id}");
                return 1;
            }
            
            // Verify deletion
            $deletedTask = Task::find($task->id);
            if (!$deletedTask) {
                $this->info("Verified: Task no longer exists in the database.");
            } else {
                $this->error("Error: Task still exists in the database after deletion.");
                return 1;
            }
            
            $this->info("\n=== All Task CRUD operations completed successfully! ===");
            return 0;
        } catch (\Exception $e) {
            $this->error('Exception occurred: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
