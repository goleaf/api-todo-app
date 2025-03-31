<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call UserSeeder to create initial users
        $this->call(UserSeeder::class);

        // Call CategorySeeder to create initial categories
        $this->call(CategorySeeder::class);

        // Call TodoSeeder to create initial tasks
        $this->call(TodoSeeder::class);

        // Only use TaskSeeder if TodoSeeder didn't create any tasks
        // This is a fallback and can be removed when TodoSeeder is fully working
        if (\App\Models\Task::count() === 0) {
            $this->call(TaskSeeder::class);
        }
    }
}
