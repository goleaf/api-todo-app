<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only proceed if data migration has been executed
        if (Schema::hasTable('migrations_log')) {
            $dataMigration = DB::table('migrations_log')
                ->where('migration', 'migrate_data_from_todos_to_tasks')
                ->first();
            
            if ($dataMigration && Schema::hasTable('todos')) {
                Schema::dropIfExists('todos');
            }
        } elseif (Schema::hasTable('todos')) {
            // If migrations_log doesn't exist, just check if the table exists
            // This is less safe but allows the migration to run in fresh installations
            Schema::dropIfExists('todos');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the todos table if needed - only structure, not data
        if (!Schema::hasTable('todos')) {
            Schema::create('todos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('session_id')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->boolean('completed')->default(false);
                $table->date('due_date')->nullable();
                $table->integer('priority')->default(0);
                $table->integer('progress')->default(0);
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
                $table->dateTime('reminder_at')->nullable();
                $table->json('tags')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'session_id']);
            });
        }
    }
};
