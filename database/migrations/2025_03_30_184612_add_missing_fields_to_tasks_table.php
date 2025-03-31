<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Add missing fields from the todos table
            if (!Schema::hasColumn('tasks', 'session_id')) {
                $table->string('session_id')->nullable()->after('user_id');
            }
            
            if (!Schema::hasColumn('tasks', 'reminder_at')) {
                $table->dateTime('reminder_at')->nullable()->after('due_date');
            }
            
            if (!Schema::hasColumn('tasks', 'tags')) {
                $table->json('tags')->nullable()->after('progress');
            }
            
            // Update index to match the one on todos table
            if (!Schema::hasIndex('tasks', ['user_id', 'session_id'])) {
                $table->index(['user_id', 'session_id']);
            }
            
            // If still using enum for status, replace it with boolean completed
            if (Schema::hasColumn('tasks', 'status')) {
                $table->dropColumn('status');
                
                if (!Schema::hasColumn('tasks', 'completed')) {
                    $table->boolean('completed')->default(false)->after('description');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Drop the added columns in reverse order
            if (Schema::hasColumn('tasks', 'tags')) {
                $table->dropColumn('tags');
            }
            
            if (Schema::hasColumn('tasks', 'reminder_at')) {
                $table->dropColumn('reminder_at');
            }
            
            if (Schema::hasColumn('tasks', 'session_id')) {
                $table->dropColumn('session_id');
            }
            
            // Drop the index
            if (Schema::hasIndex('tasks', ['user_id', 'session_id'])) {
                $table->dropIndex(['user_id', 'session_id']);
            }
            
            // Re-add the status column if it was dropped
            if (!Schema::hasColumn('tasks', 'status')) {
                $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending')->after('description');
            }
        });
    }
};
