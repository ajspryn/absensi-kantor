<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Schema;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        // Build a flat list of permission keys from available permissions
        $allPermissionKeys = collect(Role::getAvailablePermissions())
            ->flatMap(function ($group) {
                return array_keys($group);
            })
            ->values()
            ->all();

        $adminRole = Role::firstOrCreate([
            'name' => 'Admin'
        ], [
            'description' => 'System administrator',
            // store the actual permission keys (e.g. 'employees.view')
            'permissions' => $allPermissionKeys,
            'is_active' => true,
            'is_default' => false,
            'priority' => 1,
        ]);

        // Ensure existing Admin role is synced with full permissions (idempotent)
        if ($adminRole->permissions !== $allPermissionKeys) {
            $adminRole->syncPermissions($allPermissionKeys);
        }

        $employeeRole = Role::firstOrCreate([
            'name' => 'Employee'
        ], [
            'description' => 'Regular employee role',
            'permissions' => [],
            'is_active' => true,
            'is_default' => true,
            'priority' => 5,
        ]);

        $managerRole = Role::firstOrCreate([
            'name' => 'Manager'
        ], [
            'description' => 'Department manager',
            'permissions' => [],
            'is_active' => true,
            'is_default' => false,
            'priority' => 3,
        ]);

        // Departments
        $departments = [];
        foreach (['Engineering', 'Human Resources', 'Sales'] as $name) {
            $departments[$name] = Department::firstOrCreate([
                'name' => $name
            ], [
                'description' => "$name department",
                'is_active' => true,
            ]);
        }

        // Positions
        $positions = [];
        $hasPositionDept = Schema::hasColumn('positions', 'department_id');

        $positions['Engineering'] = collect([
            ['name' => 'Software Engineer', 'level' => 3],
            ['name' => 'Senior Software Engineer', 'level' => 4],
            ['name' => 'Engineering Manager', 'level' => 5],
        ])->map(function ($p) use ($departments, $hasPositionDept) {
            $where = ['name' => $p['name']];
            if ($hasPositionDept) {
                $where['department_id'] = $departments['Engineering']->id;
            }
            $attributes = ['description' => $p['name'], 'is_active' => true];
            if (Schema::hasColumn('positions', 'level')) {
                $attributes['level'] = $p['level'];
            }
            if (Schema::hasColumn('positions', 'min_salary')) {
                $attributes['min_salary'] = 5000000;
            }
            if (Schema::hasColumn('positions', 'max_salary')) {
                $attributes['max_salary'] = 20000000;
            }
            return Position::firstOrCreate($where, $attributes);
        })->all();

        $positions['Human Resources'] = collect([
            ['name' => 'HR Specialist', 'level' => 2],
            ['name' => 'HR Manager', 'level' => 5],
        ])->map(function ($p) use ($departments, $hasPositionDept) {
            $where = ['name' => $p['name']];
            if ($hasPositionDept) {
                $where['department_id'] = $departments['Human Resources']->id;
            }
            $attributes = ['description' => $p['name'], 'is_active' => true];
            if (Schema::hasColumn('positions', 'level')) {
                $attributes['level'] = $p['level'];
            }
            if (Schema::hasColumn('positions', 'min_salary')) {
                $attributes['min_salary'] = 3000000;
            }
            if (Schema::hasColumn('positions', 'max_salary')) {
                $attributes['max_salary'] = 12000000;
            }
            return Position::firstOrCreate($where, $attributes);
        })->all();

        $positions['Sales'] = collect([
            ['name' => 'Sales Representative', 'level' => 2],
            ['name' => 'Sales Manager', 'level' => 5],
        ])->map(function ($p) use ($departments, $hasPositionDept) {
            $where = ['name' => $p['name']];
            if ($hasPositionDept) {
                $where['department_id'] = $departments['Sales']->id;
            }
            $attributes = ['description' => $p['name'], 'is_active' => true];
            if (Schema::hasColumn('positions', 'level')) {
                $attributes['level'] = $p['level'];
            }
            if (Schema::hasColumn('positions', 'min_salary')) {
                $attributes['min_salary'] = 3000000;
            }
            if (Schema::hasColumn('positions', 'max_salary')) {
                $attributes['max_salary'] = 15000000;
            }
            return Position::firstOrCreate($where, $attributes);
        })->all();

        // Users
        // Admin
        $admin = User::firstOrCreate([
            'email' => 'admin@absensi.com'
        ], [
            'name' => 'Administrator',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        // Manager
        $managerUser = User::firstOrCreate([
            'email' => 'manager@absensi.com'
        ], [
            'name' => 'Manager User',
            'password' => bcrypt('password'),
            'role_id' => $managerRole->id,
            'is_active' => true,
        ]);

        // Create some employee users and employee records
        foreach (range(1, 6) as $i) {
            $u = User::firstOrCreate([
                'email' => "employee{$i}@absensi.test",
            ], [
                'name' => "Employee $i",
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'role_id' => $employeeRole->id,
                'is_active' => true,
                'remember_token' => Str::random(10),
            ]);

            // Assign department and position round-robin
            $deptNames = array_keys($departments);
            $deptName = $deptNames[$i % count($deptNames)];
            $dept = $departments[$deptName];
            $posArr = $positions[$deptName];
            $pos = $posArr[array_rand($posArr)];

            // Create employee record only if not exists for this user
            if (! Employee::where('user_id', $u->id)->exists()) {
                $data = [
                    'user_id' => $u->id,
                    'department_id' => $dept->id,
                    'full_name' => $u->name,
                    'email' => $u->email,
                ];
                if (Schema::hasColumn('employees', 'position_id')) {
                    $data['position_id'] = $pos->id ?? null;
                }
                Employee::factory()->create($data);
            }
        }

        $this->command->info('Dummy data created:');
        $this->command->line('  Roles: ' . Role::count());
        $this->command->line('  Departments: ' . Department::count());
        $this->command->line('  Positions: ' . Position::count());
        $this->command->line('  Users: ' . User::count());
        $this->command->line('  Employees: ' . Employee::count());
    }
}
