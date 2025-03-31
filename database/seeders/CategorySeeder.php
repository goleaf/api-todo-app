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
        // Create sample categories for each user
        $users = User::all();

        $categoryTemplates = [
            [
                'name' => 'Work',
                'color' => '#4f46e5', // Indigo
                'icon' => 'briefcase',
            ],
            [
                'name' => 'Personal',
                'color' => '#16a34a', // Green
                'icon' => 'user',
            ],
            [
                'name' => 'Health',
                'color' => '#dc2626', // Red
                'icon' => 'heart',
            ],
            [
                'name' => 'Education',
                'color' => '#9333ea', // Purple
                'icon' => 'academic-cap',
            ],
            [
                'name' => 'Finance',
                'color' => '#0284c7', // Blue
                'icon' => 'currency-dollar',
            ],
        ];

        foreach ($users as $user) {
            // Create categories for this user
            foreach ($categoryTemplates as $template) {
                // Only create if it doesn't exist
                if (! Category::where('user_id', $user->id)->where('name', $template['name'])->exists()) {
                    Category::create([
                        'name' => $template['name'],
                        'color' => $template['color'],
                        'icon' => $template['icon'],
                        'user_id' => $user->id,
                    ]);
                }
            }
        }
    }
}
