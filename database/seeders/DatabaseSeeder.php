<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('Admin@123'),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        // Create Regular User
        User::create([
            'name' => 'Regular User',
            'username' => 'user',
            'email' => 'user@example.com',
            'password' => Hash::make('User@123'),
            'is_admin' => false,
            'email_verified_at' => now(),
        ]);
    }
}
