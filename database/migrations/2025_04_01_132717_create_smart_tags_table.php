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
        Schema::create('smart_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#8b5cf6'); // Default purple color
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Smart tag criteria fields (JSON for maximum flexibility)
            $table->json('criteria')->nullable();
            $table->text('description')->nullable();
            
            // Fields for common filter criteria
            $table->boolean('filter_by_due_date')->default(false);
            $table->string('due_date_operator')->nullable(); // 'today', 'this_week', 'overdue', 'custom'
            $table->json('due_date_values')->nullable(); // For custom date ranges
            
            $table->boolean('filter_by_priority')->default(false);
            $table->json('priority_values')->nullable(); // [1, 2, 3] for multiple priorities
            
            $table->boolean('filter_by_category')->default(false);
            $table->json('category_ids')->nullable(); // Array of category IDs to include
            
            $table->boolean('filter_by_status')->default(false);
            $table->boolean('status_completed')->nullable(); // true = completed, false = incomplete
            
            $table->timestamps();
            
            // Ensure unique names per user
            $table->unique(['name', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smart_tags');
    }
};
