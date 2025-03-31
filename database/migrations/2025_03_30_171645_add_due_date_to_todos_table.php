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
        Schema::table('todos', function (Blueprint $table) {
            // Skip due_date as it's added in previous migrations
            
            if (!Schema::hasColumn('todos', 'session_id')) {
                $table->string('session_id')->nullable();
            }
            
            // Make user_id nullable if it's not already
            if (Schema::hasColumn('todos', 'user_id')) {
                $table->foreignId('user_id')->nullable()->change();
            }
            
            // Add combined index for user_id and session_id if not exists
            if (!Schema::hasIndex('todos', ['user_id', 'session_id'])) {
                $table->index(['user_id', 'session_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            // Don't drop due_date as it was not added in this migration
            $table->dropColumn('session_id');
            $table->dropIndex(['user_id', 'session_id']);
            
            // Make user_id required again
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
