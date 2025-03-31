<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
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
        $categories = [
            'Work' => CategoryType::WORK,
            'Personal' => CategoryType::PERSONAL,
            'Education' => CategoryType::EDUCATION,
            'Health' => CategoryType::HEALTH,
            'Finance' => CategoryType::FINANCE,
            'Home' => CategoryType::HOME,
            'Other' => CategoryType::OTHER,
        ];

        User::all()->each(function (User $user) use ($categories) {
            foreach ($categories as $name => $type) {
                Category::create([
                    'name' => $name,
                    'type' => $type,
                    'user_id' => $user->id,
                    'description' => fake()->sentence(),
                    'color' => fake()->hexColor(),
                ]);
            }
        });
    }
}
