<?php

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $dept = Department::first() ?: Department::create(['name' => 'Temp Dept', 'is_active' => 1]);
    $role = Role::first() ?: (Role::find(2) ?: null);

    $user = User::create([
        'name' => 'Auto Test',
        'email' => 'autotest.'.time().'@example.com',
        'password' => Hash::make('secret123'),
        'role_id' => $role ? $role->id : 2,
        'is_active' => 1,
    ]);

    $emp = Employee::create([
        'employee_id' => 'TST'.time(),
        'user_id' => $user->id,
        'department_id' => $dept->id,
        'position_id' => null,
        'full_name' => 'Auto Test',
        'email' => $user->email,
        'hire_date' => date('Y-m-d'),
        'is_active' => 1,
        'nik_ktp' => '1234567890',
        'mobile' => '08123456789',
        'gender' => 'M',
        'birth_place' => 'City',
        'birth_date' => '1990-01-01',
        'health_condition' => 'Sehat',
        'education_history' => ['s1' => ['name' => 'Test Univ', 'year' => 2012]],
    ]);

    $fresh = Employee::with('user')->find($emp->id);
    echo json_encode($fresh->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    echo 'Error: '.$e->getMessage().PHP_EOL;
    echo $e->getTraceAsString();
}
