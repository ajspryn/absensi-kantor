<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
     {
          Schema::create('family_members', function (Blueprint $table) {
               $table->id();
               $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
               $table->string('relation')->nullable();
               $table->string('name')->nullable();
               $table->string('gender')->nullable();
               $table->string('last_education')->nullable();
               $table->string('last_job')->nullable();
               $table->integer('age')->nullable();
               $table->timestamps();
          });
     }

     public function down()
     {
          Schema::dropIfExists('family_members');
     }
};
