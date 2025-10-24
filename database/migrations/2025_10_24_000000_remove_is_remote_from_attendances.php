<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
     public function up(): void
     {
          if (Schema::hasTable('attendances') && Schema::hasColumn('attendances', 'is_remote')) {
               Schema::table('attendances', function (Blueprint $table) {
                    $table->dropColumn('is_remote');
               });
          }
     }

     public function down(): void
     {
          if (Schema::hasTable('attendances') && !Schema::hasColumn('attendances', 'is_remote')) {
               Schema::table('attendances', function (Blueprint $table) {
                    $table->boolean('is_remote')->default(false)->after('status')->nullable();
               });
          }
     }
};
