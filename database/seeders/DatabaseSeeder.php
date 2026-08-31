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
        // 1. Kutengeneza Akaunti ya Librarian
        User::create([
            'name' => 'Librarian Admin',
            'email' => 'librarian@urms.ac.tz',
            'password' => Hash::make('password123'), // Nenosiri
            'role' => 'librarian',
            'email_verified_at' => now(),
        ]);

        // 2. Kutengeneza Akaunti ya Supervisor
        User::create([
            'name' => 'Dr. Supervisor',
            'email' => 'supervisor@urms.ac.tz',
            'password' => Hash::make('password123'), // Nenosiri
            'role' => 'supervisor',
            'email_verified_at' => now(),
        ]);

        // 3. Kutengeneza Akaunti ya Mwanafunzi (Kwa ajili ya Testing)
        User::create([
            'name' => 'Student Demo',
            'email' => 'student@urms.ac.tz',
            'password' => Hash::make('password123'), // Nenosiri
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
    }
}