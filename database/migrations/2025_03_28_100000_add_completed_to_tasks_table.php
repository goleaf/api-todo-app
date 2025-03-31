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
        try {
            Schema::table('tasks', function (Blueprint $table) {
                if (!Schema::hasColumn('tasks', 'completed')) {
                    $table->boolean('completed')->default(false);
                }
            });
        } catch (\Exception $e) {
            // Column might already exist or other errors
            // Log error for debugging if needed
            // echo $e->getMessage();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'completed')) {
                $table->dropColumn('completed');
            }
        });
    }
};
