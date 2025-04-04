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
        Schema::table('smart_tags', function (Blueprint $table) {
            // Add status_pending and status_in_progress fields
            $table->boolean('status_pending')->nullable()->after('status_completed');
            $table->boolean('status_in_progress')->nullable()->after('status_pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smart_tags', function (Blueprint $table) {
            $table->dropColumn(['status_pending', 'status_in_progress']);
        });
    }
}; 