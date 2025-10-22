<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Create admin user
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@absensi.com',
            'role_id' => 1, // admin role
            'password' => bcrypt('password'),
        ]);

        // Create test employee user
        User::factory()->create([
            'name' => 'Test Employee',
            'email' => 'employee@absensi.com',
            'role_id' => 2, // employee role
            'password' => bcrypt('password'),
        ]);
    }
}
