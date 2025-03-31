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
            if (!Schema::hasColumn('todos', 'due_date')) {
                $table->datetime('due_date')->nullable();
            }
            
            if (!Schema::hasColumn('todos', 'reminder_at')) {
                $table->datetime('reminder_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            if (Schema::hasColumn('todos', 'due_date')) {
                $table->dropColumn('due_date');
            }
            
            if (Schema::hasColumn('todos', 'reminder_at')) {
                $table->dropColumn('reminder_at');
            }
        });
    }
};
