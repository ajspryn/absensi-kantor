<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FillPositionIdFromPositionName extends Migration
{
     /**
      * Run the migrations.
      * Iterate positions and employees to safely update position_id (SQLite-safe).
      */
     public function up()
     {
          $positions = DB::table('positions')->select('id', 'name')->get();
          $map = [];
          foreach ($positions as $p) {
               $map[strtolower(trim($p->name))] = $p->id;
          }

          $employees = DB::table('employees')
               ->whereNotNull('position')
               ->whereNull('position_id')
               ->select('id', 'position')
               ->get();

          foreach ($employees as $e) {
               $key = strtolower(trim($e->position));
               if (isset($map[$key])) {
                    DB::table('employees')->where('id', $e->id)->update([
                         'position_id' => $map[$key],
                    ]);
               }
          }
     }

     /**
      * Reverse the migrations.
      * Best-effort: nullify position_id for rows that were set from matching position names.
      */
     public function down()
     {
          $positions = DB::table('positions')->select('id', 'name')->get();
          $mapIds = [];
          foreach ($positions as $p) {
               $mapIds[] = $p->id;
          }

          if (!empty($mapIds)) {
               DB::table('employees')
                    ->whereNotNull('position')
                    ->whereIn('position_id', $mapIds)
                    ->update(['position_id' => null]);
          }
     }
}
