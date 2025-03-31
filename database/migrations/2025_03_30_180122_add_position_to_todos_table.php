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
        Schema::table('todos', function (Blueprint $table) {
            $table->integer('position')->nullable()->after('due_date');
        });
        
        // Set initial positions for existing todos
        $todos = DB::table('todos')->orderBy('created_at')->get();
        
        $position = 1;
        foreach ($todos as $todo) {
            DB::table('todos')->where('id', $todo->id)->update(['position' => $position]);
            $position++;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
