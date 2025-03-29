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
            $table->timestamp('due_date')->nullable()->after('completed');
            $table->timestamp('reminder_at')->nullable()->after('due_date');
            $table->integer('priority')->default(0)->after('reminder_at')->comment('0: Low, 1: Medium, 2: High');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'reminder_at', 'priority']);
        });
    }
};
