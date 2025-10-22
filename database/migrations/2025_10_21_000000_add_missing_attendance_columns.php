<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
     public function up(): void
     {
          if (Schema::hasTable('attendances')) {
               // Add is_remote column if it doesn't exist
               if (!Schema::hasColumn('attendances', 'is_remote')) {
                    Schema::table('attendances', function (Blueprint $table) {
                         $table->boolean('is_remote')->default(false)->after('status')->nullable();
                    });
               }

               // Add office_location_name for backward compatibility with older code
               if (!Schema::hasColumn('attendances', 'office_location_name')) {
                    Schema::table('attendances', function (Blueprint $table) {
                         $table->string('office_location_name')->nullable()->after('office_location_id');
                    });
               }
          }
     }

     public function down(): void
     {
          if (Schema::hasTable('attendances')) {
               Schema::table('attendances', function (Blueprint $table) {
                    if (Schema::hasColumn('attendances', 'is_remote')) {
                         $table->dropColumn('is_remote');
                    }
                    if (Schema::hasColumn('attendances', 'office_location_name')) {
                         $table->dropColumn('office_location_name');
                    }
               });
          }
     }
};
