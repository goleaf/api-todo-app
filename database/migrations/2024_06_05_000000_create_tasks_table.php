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
        Schema::dropIfExists('tasks');
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedTinyInteger('priority')->default(1); // 1=Low, 2=Medium, 3=High, 4=Urgent
            $table->boolean('completed')->default(false);
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            // Add indexes for common query patterns
            $table->index(['user_id', 'completed']);
            $table->index(['user_id', 'due_date']);
            $table->index(['user_id', 'priority']);
            $table->index(['user_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
