<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@todo.prus.dev',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
        ]);

        // Create demo user
        User::create([
            'name' => 'Demo User',
            'email' => 'demo@todo.prus.dev',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'timezone' => 'UTC',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
        ]);

        // Create additional test users
        User::factory(8)->create();
    }
} 