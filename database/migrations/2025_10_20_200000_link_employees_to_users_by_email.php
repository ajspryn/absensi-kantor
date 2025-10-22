<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
     /**
      * Run the migrations.
      */
     public function up(): void
     {
          // Link employees to users when emails match and user_id is null.
          // Some databases (SQLite) don't support update with join, so do this safely.
          $pairs = DB::table('employees')
               ->select('employees.id as emp_id', 'users.id as user_id')
               ->whereNull('employees.user_id')
               ->whereNotNull('employees.email')
               ->join('users', 'users.email', '=', 'employees.email')
               ->get();

          $count = 0;
          foreach ($pairs as $p) {
               DB::table('employees')->where('id', $p->emp_id)->update(['user_id' => $p->user_id]);
               $count++;
          }

          \Illuminate\Support\Facades\Log::info("Linked employees to users by email, rows affected: " . $count);
     }

     /**
      * Reverse the migrations.
      */
     public function down(): void
     {
          // Best-effort rollback: set user_id to null where the users email equals employees.email
          DB::table('employees')
               ->join('users', 'users.email', '=', 'employees.email')
               ->whereNotNull('employees.user_id')
               ->update(['employees.user_id' => null]);
     }
};
