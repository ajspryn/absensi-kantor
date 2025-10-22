<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
     {
          Schema::create('positions', function (Blueprint $table) {
               $table->id();
               $table->string('name');
               $table->text('description')->nullable();
               $table->boolean('is_active')->default(true);
               $table->timestamps();
          });

          Schema::create('work_schedules', function (Blueprint $table) {
               $table->id();
               $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
               $table->string('name');
               $table->text('description')->nullable();
               $table->time('start_time');
               $table->time('end_time');
               $table->time('break_start_time')->nullable();
               $table->time('break_end_time')->nullable();
               $table->json('work_days');
               $table->boolean('is_flexible')->default(false);
               $table->boolean('location_required')->default(true);
               $table->boolean('is_active')->default(true);
               $table->date('effective_date')->nullable();
               $table->date('end_date')->nullable();
               $table->decimal('total_hours', 4, 2)->nullable();
               $table->decimal('overtime_threshold', 4, 2)->nullable();
               $table->integer('late_tolerance')->default(15)->comment('Toleransi keterlambatan dalam menit');
               $table->timestamps();
               $table->index(['user_id', 'is_active']);
               $table->index(['effective_date', 'end_date']);
          });
     }

     public function down(): void
     {
          Schema::dropIfExists('work_schedules');
          Schema::dropIfExists('positions');
     }
};
