<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ConvertDepartmentManagerIds extends Migration
{
     /**
      * Run the migrations.
      *
      * This migrates legacy data where departments.manager_id stored an employees.id
      * into the newer shape where manager_id references users.id (employees.user_id).
      */
     public function up()
     {
          if (!Schema::hasTable('departments') || !Schema::hasTable('employees')) {
               return;
          }

          // Find departments where manager_id matches an employees.id and update to employees.user_id
          $rows = DB::table('departments')
               ->join('employees', 'employees.id', '=', 'departments.manager_id')
               ->select('departments.id as dept_id', 'employees.user_id')
               ->get();

          foreach ($rows as $row) {
               if ($row->user_id) {
                    DB::table('departments')
                         ->where('id', $row->dept_id)
                         ->update(['manager_id' => $row->user_id]);
               }
          }
     }

     /**
      * Reverse the migrations.
      *
      * We cannot reliably map back to employees.id so we leave manager_id as-is.
      */
     public function down()
     {
          // no-op (intentional)
     }
}
