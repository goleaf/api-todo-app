<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->string('filename', 255);
            $table->string('path', 255);
            $table->string('mime_type', 100);
            $table->unsignedInteger('size');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index(['user_id', 'task_id']);
            $table->index(['user_id', 'mime_type']);
            $table->index('created_at');
            $table->index('deleted_at');
        });

        // Set table options for better performance
        DB::statement('ALTER TABLE attachments ROW_FORMAT=DYNAMIC');
        DB::statement('ALTER TABLE attachments ENGINE=InnoDB');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
}; 