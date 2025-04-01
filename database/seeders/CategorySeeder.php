<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Work', 'color' => '#FF0000'],
            ['name' => 'Personal', 'color' => '#00FF00'],
            ['name' => 'Shopping', 'color' => '#0000FF'],
            ['name' => 'Health', 'color' => '#FFFF00'],
            ['name' => 'Education', 'color' => '#FF00FF'],
        ];

        User::all()->each(function ($user) use ($categories) {
            foreach ($categories as $category) {
                Category::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'name' => $category['name']
                    ],
                    [
                        'color' => $category['color']
                    ]
                );
            }
        });
    }
} 