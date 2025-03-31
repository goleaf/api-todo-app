<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 random users with usernames as emails
        for ($i = 1; $i <= 10; $i++) {
            $username = 'user' . $i;
            User::create([
                'name' => 'Test User ' . $i,
                'email' => $username . '@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        }
    }
}
