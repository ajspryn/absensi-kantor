<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
     {
          Schema::create('employees', function (Blueprint $table) {
               $table->id();
               $table->string('employee_id')->unique();
               $table->foreignId('user_id')->constrained()->onDelete('cascade');
               $table->foreignId('department_id')->constrained()->onDelete('restrict');
               $table->string('full_name');
               $table->string('position');
               $table->foreignId('position_id')->nullable()->constrained()->onDelete('set null');
               $table->string('phone')->nullable();
               $table->string('email')->nullable();
               $table->text('address')->nullable();
               $table->date('hire_date');
               $table->decimal('salary', 12, 2)->nullable();
               $table->boolean('is_active')->default(true);
               $table->boolean('allow_remote_attendance')->default(false);
               $table->string('photo')->nullable();
               $table->foreignId('work_schedule_id')->nullable()->constrained('work_schedules')->onDelete('set null');
               $table->timestamps();
          });
     }

     public function down(): void
     {
          Schema::dropIfExists('employees');
     }
};
