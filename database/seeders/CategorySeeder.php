<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCategories = [
            'Work' => '#FF0000',
            'Personal' => '#00FF00',
            'Shopping' => '#0000FF',
            'Health' => '#FFA500',
            'Education' => '#800080',
        ];

        User::all()->each(function ($user) use ($defaultCategories) {
            // Create default categories for each user
            foreach ($defaultCategories as $name => $color) {
                Category::create([
                    'user_id' => $user->id,
                    'name' => $name,
                    'color' => $color,
                ]);
            }

            // Create additional random categories
            Category::factory(3)->create([
                'user_id' => $user->id,
            ]);
        });
    }
} 