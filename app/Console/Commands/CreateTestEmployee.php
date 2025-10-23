<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\Hash;

class CreateTestEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:test-employee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test employee user for testing role permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create or get test department
        $department = Department::firstOrCreate([
            'name' => 'Test Department'
        ], [
            'description' => 'Department for testing'
        ]);

        // Create or get test position
        $position = Position::firstOrCreate([
            'name' => 'Test Employee'
        ], [
            'description' => 'Position for testing',
            'department_id' => $department->id
        ]);

        // Create test user
        $user = User::firstOrCreate([
            'email' => 'employee@test.com'
        ], [
            'name' => 'Test Employee',
            'password' => Hash::make('password123'),
            'role_id' => 3, // Employee role
            'email_verified_at' => now()
        ]);

        // Create employee record
        $employee = Employee::firstOrCreate([
            'user_id' => $user->id
        ], [
            'employee_id' => 'EMP001',
            'full_name' => 'Test Employee',
            'department_id' => $department->id,
            'position_id' => $position->id,
            'hire_date' => now(),
            'is_active' => true,
            'phone' => '081234567890',
            'email' => 'employee@test.com',
            'address' => 'Test Address'
        ]);

        $this->info("Test employee created successfully!");
        $this->info("Email: employee@test.com");
        $this->info("Password: password123");
        $this->info("Role: Employee (ID: 3)");
    }
}
