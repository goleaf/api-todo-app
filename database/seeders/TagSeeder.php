<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Urgent',
            'Important',
            'Meeting',
            'Email',
            'Call',
            'Appointment',
            'Project',
            'Research',
            'Reading',
            'Writing',
        ];

        User::all()->each(function (User $user) use ($tags) {
            foreach ($tags as $tagName) {
                Tag::create([
                    'name' => $tagName,
                    'user_id' => $user->id,
                    'color' => fake()->hexColor(),
                ]);
            }
        });
    }
} 