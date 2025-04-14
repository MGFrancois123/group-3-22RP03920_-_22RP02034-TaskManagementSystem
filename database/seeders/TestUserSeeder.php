<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 test users
        for ($i = 1; $i <= 5; $i++) {
            $email = "user{$i}@example.com";
            if (!User::where('email', $email)->exists()) {
                User::create([
                    'name' => "Test User {$i}",
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'user',
                ]);
            }
        }
    }
} 