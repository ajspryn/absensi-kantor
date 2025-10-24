<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DailyActivityPermissionsSeeder extends Seeder
{
     /**
      * Run the database seeds.
      */
     public function run(): void
     {
          // Add daily activities permissions to existing roles where appropriate
          $permissions = [
               'daily_activities.create',
               'daily_activities.view_own',
          ];

          // Give these to the default role (if exists)
          $defaultRole = DB::table('roles')->where('is_default', 1)->first();
          if ($defaultRole) {
               $role = json_decode($defaultRole->permissions ?? '[]', true);
               $merged = array_values(array_unique(array_merge($role, $permissions)));
               DB::table('roles')->where('id', $defaultRole->id)->update(['permissions' => json_encode($merged)]);
          }

          // Ensure manager role(s) have department view and export/approve rights
          $managerRoles = DB::table('roles')->where('name', 'like', '%Manager%')->get();
          foreach ($managerRoles as $r) {
               $role = json_decode($r->permissions ?? '[]', true);
               $add = ['daily_activities.view_department', 'daily_activities.export'];
               $merged = array_values(array_unique(array_merge($role, $add)));
               DB::table('roles')->where('id', $r->id)->update(['permissions' => json_encode($merged)]);
          }
     }
}
