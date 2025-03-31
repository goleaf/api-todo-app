<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key constraints
        Schema::disableForeignKeyConstraints();
        
        // Truncate tables
        \App\Models\User::truncate();
        \App\Models\Admin::truncate();
        \App\Models\Category::truncate();
        \App\Models\Tag::truncate();
        \App\Models\Task::truncate();
        DB::table('task_tag')->truncate();
        
        // Enable foreign key constraints
        Schema::enableForeignKeyConstraints();
        
        // Run all seeders in the correct order
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            TaskSeeder::class,
        ]);
    }
}
