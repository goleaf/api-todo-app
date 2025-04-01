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
        $defaultTags = [
            'Urgent' => '#FF0000',
            'Important' => '#FFA500',
            'Low Priority' => '#00FF00',
            'Meeting' => '#0000FF',
            'Follow-up' => '#800080',
        ];

        User::all()->each(function ($user) use ($defaultTags) {
            // Create default tags for each user
            foreach ($defaultTags as $name => $color) {
                Tag::create([
                    'user_id' => $user->id,
                    'name' => $name,
                    'color' => $color,
                ]);
            }

            // Create additional random tags
            Tag::factory(3)->create([
                'user_id' => $user->id,
            ]);
        });
    }
} 