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
            // Add priority column if it doesn't exist
            if (!Schema::hasColumn('todos', 'priority')) {
                $table->integer('priority')->default(0)->after('completed');
            }
            
            // Add tags column if it doesn't exist
            if (!Schema::hasColumn('todos', 'tags')) {
                $table->json('tags')->nullable()->after('progress');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            if (Schema::hasColumn('todos', 'priority')) {
                $table->dropColumn('priority');
            }
            
            if (Schema::hasColumn('todos', 'tags')) {
                $table->dropColumn('tags');
            }
        });
    }
};
