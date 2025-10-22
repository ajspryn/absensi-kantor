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
          Schema::create('app_settings', function (Blueprint $table) {
               $table->id();
               $table->string('key')->unique();
               $table->text('value');
               $table->string('type')->default('text'); // text, number, boolean, json
               $table->string('group')->default('general'); // general, attendance, notifications, etc
               $table->string('label');
               $table->text('description')->nullable();
               $table->boolean('is_public')->default(false); // if true, can be accessed by employees
               $table->timestamps();
          });
     }

     /**
      * Reverse the migrations.
      */
     public function down(): void
     {
          Schema::dropIfExists('app_settings');
     }
};
