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
        // Create default admin user for testing
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('password123'),
            ]
        );

        // Create additional test user
        User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'     => 'Test User',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
