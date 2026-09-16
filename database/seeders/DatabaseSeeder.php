<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permissions = collect(Role::getAvailablePermissions())
            ->flatMap(fn (array $group) => array_keys($group))
            ->values()
            ->all();

        $adminRole = Role::updateOrCreate([
            'name' => 'Admin',
        ], [
            'description' => 'System administrator',
            'permissions' => $permissions,
            'is_active' => true,
            'is_default' => false,
            'priority' => 1,
        ]);

        User::updateOrCreate([
            'email' => 'admin@absensi.com',
        ], [
            'name' => 'Administrator',
            'role_id' => $adminRole->id,
            'password' => 'password',
        ]);
    }
}
