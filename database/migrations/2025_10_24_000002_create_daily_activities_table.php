<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
     public function up(): void
     {
          Schema::create('daily_activities', function (Blueprint $table) {
               $table->bigIncrements('id');
               $table->unsignedBigInteger('employee_id')->index();
               $table->date('date');
               $table->time('start_time')->nullable();
               $table->time('end_time')->nullable();
               $table->string('title', 255);
               $table->text('description')->nullable();
               $table->json('tasks')->nullable();
               $table->json('attachments')->nullable();
               $table->string('status', 50)->default('submitted');
               $table->timestamps();

               $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
          });
     }

     public function down(): void
     {
          Schema::dropIfExists('daily_activities');
     }
};
