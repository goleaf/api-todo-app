<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the main admin
        Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@todo.com',
            'password' => Hash::make(env('ADMIN_PASSWORD', 'change_me_in_env_file')),
            'email_verified_at' => now(),
        ]);

        // Create additional fake admins if needed
        Admin::factory(2)->create();
    }
}
