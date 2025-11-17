<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('type'); // e.g., 'annual', 'sick', 'personal'
            $table->text('reason')->nullable();
            $table->string('attachment_path')->nullable();

            // Approval workflow fields
            $table->string('status')->default('pending'); // pending, approved, verified, rejected
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->unsignedBigInteger('verifier_id')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->unsignedBigInteger('rejected_by_id')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->dateTime('rejected_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['employee_id', 'start_date']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
