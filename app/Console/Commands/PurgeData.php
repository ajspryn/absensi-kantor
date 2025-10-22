<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Position;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;

class PurgeData extends Command
{
     /**
      * The name and signature of the console command.
      *
      * @var string
      */
     protected $signature = 'app:purge-data {--dry-run} {--yes}';

     /**
      * The console command description.
      *
      * @var string
      */
     protected $description = 'Purge positions, employees and non-admin users from the database. Use --dry-run to preview';

     public function handle()
     {
          $this->info('Preparing purge preview...');

          // Identify admin roles by name (case-insensitive)
          $systemRoleNames = ['admin', 'super admin', 'system'];
          $roles = Role::all();
          $adminRoleIds = $roles->filter(function ($r) use ($systemRoleNames) {
               return in_array(strtolower($r->name), $systemRoleNames);
          })->pluck('id')->all();

          // Admin users (by role)
          $adminUsers = User::whereIn('role_id', $adminRoleIds)->get();
          $adminUserIds = $adminUsers->pluck('id')->all();

          $positionsCount = Position::count();
          $employeesCount = Employee::count();
          $usersCount = User::count();
          $adminCount = count($adminUserIds);

          $this->line("Positions: $positionsCount");
          $this->line("Employees: $employeesCount");
          $this->line("Users: $usersCount (admins preserved: $adminCount)");

          if ($adminCount > 0) {
               $this->info('Admin users (will be preserved):');
               foreach ($adminUsers as $u) {
                    $this->line(" - {$u->id} | {$u->name} <{$u->email}> (role_id: {$u->role_id})");
               }
          } else {
               $this->warn('No admin users found by role names (admin/super admin/system). Please confirm manually.');
          }

          if ($this->option('dry-run')) {
               $this->info('Dry-run mode: no changes were made.');
               return 0;
          }

          if (!$this->option('yes')) {
               if (!$this->confirm('Proceed with deletion? This will DELETE positions, employees and non-admin users.')) {
                    $this->info('Aborted by user.');
                    return 1;
               }
          }

          DB::transaction(function () use ($adminUserIds) {
               // Delete employees
               Employee::query()->delete();

               // Delete positions
               Position::query()->delete();

               // Delete users except admin users
               if (!empty($adminUserIds)) {
                    User::whereNotIn('id', $adminUserIds)->delete();
               } else {
                    // If no admins identified, do not delete anyone (safety)
                    throw new \Exception('No admin users identified; aborting purge for safety.');
               }
          });

          $this->info('Purge completed: positions, employees and non-admin users removed.');
          return 0;
     }
}
