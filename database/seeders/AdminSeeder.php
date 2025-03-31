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
        // Create the main admin with simple password
        Admin::create([
            'email' => 'admin@todo.com',
            'password' => Hash::make('admin123'),
        ]);

        // Create additional fake admins if needed
        Admin::factory(2)->create();
    }
}
