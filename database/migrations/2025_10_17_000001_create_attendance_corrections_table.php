<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
     public function up(): void
     {
          Schema::create('attendance_corrections', function (Blueprint $table) {
               $table->id();
               $table->unsignedBigInteger('user_id');
               $table->unsignedBigInteger('employee_id');
               $table->unsignedBigInteger('attendance_id')->nullable();
               $table->date('date');
               $table->dateTime('original_check_in')->nullable();
               $table->dateTime('original_check_out')->nullable();
               $table->dateTime('corrected_check_in')->nullable();
               $table->dateTime('corrected_check_out')->nullable();
               $table->text('reason')->nullable();
               $table->string('attachment_path')->nullable();

               // Approval workflow fields
               $table->string('status')->default('pending'); // pending, manager_approved, hr_approved, approved, rejected
               $table->unsignedBigInteger('manager_approver_id')->nullable();
               $table->dateTime('manager_approved_at')->nullable();
               $table->unsignedBigInteger('hr_approver_id')->nullable();
               $table->dateTime('hr_approved_at')->nullable();
               $table->unsignedBigInteger('rejected_by_id')->nullable();
               $table->text('rejected_reason')->nullable();
               $table->dateTime('rejected_at')->nullable();

               $table->timestamps();

               // Indexes
               $table->index(['employee_id', 'date']);
               $table->index(['status']);
          });
     }

     public function down(): void
     {
          Schema::dropIfExists('attendance_corrections');
     }
};
