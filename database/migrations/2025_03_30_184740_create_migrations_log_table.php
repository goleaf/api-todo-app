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
        if (! Schema::hasTable('migrations_log')) {
            Schema::create('migrations_log', function (Blueprint $table) {
                $table->id();
                $table->string('migration');
                $table->text('description')->nullable();
                $table->timestamp('migrated_at');
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index('migration');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('migrations_log');
    }
};
