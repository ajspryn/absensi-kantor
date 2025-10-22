<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
     {
          Schema::create('attendances', function (Blueprint $table) {
               $table->id();
               $table->foreignId('employee_id')->constrained()->onDelete('cascade');
               $table->date('date');
               $table->time('check_in')->nullable();
               $table->time('check_out')->nullable();
               $table->decimal('latitude_in', 10, 8)->nullable();
               $table->decimal('longitude_in', 11, 8)->nullable();
               $table->decimal('latitude_out', 10, 8)->nullable();
               $table->decimal('longitude_out', 11, 8)->nullable();
               $table->string('photo_in')->nullable();
               $table->string('photo_out')->nullable();
               $table->text('notes')->nullable();
               $table->enum('status', ['present', 'late', 'absent', 'permission'])->default('present');
               $table->integer('working_hours')->nullable();
               $table->foreignId('office_location_id')->nullable()->constrained('office_locations')->onDelete('set null');
               $table->string('location_name')->nullable();
               $table->foreignId('work_schedule_id')->nullable()->constrained()->onDelete('set null');
               $table->enum('schedule_status', ['on_time', 'late', 'early_leave', 'late_early_leave'])->default('on_time');
               $table->integer('late_minutes')->default(0);
               $table->integer('early_leave_minutes')->default(0);
               $table->timestamps();
               $table->unique(['employee_id', 'date']);
          });
     }

     public function down(): void
     {
          Schema::dropIfExists('attendances');
     }
};
