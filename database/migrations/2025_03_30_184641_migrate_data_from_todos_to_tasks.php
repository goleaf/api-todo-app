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
        // Create migrations_log table if it doesn't exist
        if (! Schema::hasTable('migrations_log')) {
            Schema::create('migrations_log', function ($table) {
                $table->id();
                $table->string('migration');
                $table->timestamp('migrated_at');
            });
        }

        // Only execute if both tables exist
        if (Schema::hasTable('todos') && Schema::hasTable('tasks')) {
            // Get all todos
            $todos = DB::table('todos')->get();

            foreach ($todos as $todo) {
                // Check if a task with the same title and user_id already exists
                $existingTask = DB::table('tasks')
                    ->where('title', $todo->title)
                    ->where('user_id', $todo->user_id)
                    ->first();

                if (! $existingTask) {
                    // Map todo data to task structure
                    $taskData = [
                        'user_id' => $todo->user_id,
                        'session_id' => $todo->session_id ?? null,
                        'title' => $todo->title,
                        'description' => $todo->description,
                        'completed' => $todo->completed ?? false,
                        'completed_at' => $todo->completed ? now() : null,
                        'due_date' => $todo->due_date,
                        'created_at' => $todo->created_at,
                        'updated_at' => $todo->updated_at,
                    ];

                    // Add extra fields if they exist in both tables
                    if (Schema::hasColumn('todos', 'category_id') && Schema::hasColumn('tasks', 'category_id')) {
                        $taskData['category_id'] = $todo->category_id ?? null;
                    }

                    if (Schema::hasColumn('todos', 'priority') && Schema::hasColumn('tasks', 'priority')) {
                        $taskData['priority'] = $todo->priority ?? 0;
                    }

                    if (Schema::hasColumn('todos', 'progress') && Schema::hasColumn('tasks', 'progress')) {
                        $taskData['progress'] = $todo->progress ?? 0;
                    }

                    if (Schema::hasColumn('todos', 'reminder_at') && Schema::hasColumn('tasks', 'reminder_at')) {
                        $taskData['reminder_at'] = $todo->reminder_at ?? null;
                    }

                    if (Schema::hasColumn('todos', 'tags') && Schema::hasColumn('tasks', 'tags')) {
                        $taskData['tags'] = $todo->tags ?? '[]';
                    }

                    // Insert the task
                    DB::table('tasks')->insert($taskData);
                }
            }

            // Log migration completion
            DB::table('migrations_log')->insert([
                'migration' => 'migrate_data_from_todos_to_tasks',
                'migrated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     * Note: This is a data migration and cannot be safely reversed
     */
    public function down(): void
    {
        // This is a data migration and cannot be safely reversed
        // If you need to reverse it, restore from a backup
    }
};
