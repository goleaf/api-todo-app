<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run this migration if both tables exist
        if (Schema::hasTable('todos') && Schema::hasTable('tasks')) {
            // Get all the todos
            $todos = DB::table('todos')->get();
            
            foreach ($todos as $todo) {
                // Map old priority to new priority scale
                $priority = match ($todo->priority ?? 0) {
                    0 => 1, // Low
                    1 => 2, // Medium
                    2 => 3, // High
                    default => 1, // Default to Low
                };
                
                // Insert into new tasks table
                DB::table('tasks')->insert([
                    'user_id' => $todo->user_id,
                    'category_id' => $todo->category_id,
                    'title' => $todo->title,
                    'description' => $todo->description,
                    'due_date' => $todo->due_date,
                    'priority' => $priority,
                    'completed' => $todo->completed ?? false,
                    'tags' => $todo->tags,
                    'notes' => null, // New field
                    'attachments' => null, // New field
                    'created_at' => $todo->created_at,
                    'updated_at' => $todo->updated_at,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse migration needed, as we're keeping both tables.
        // If desired, data could be moved back from tasks to todos.
    }
}; 