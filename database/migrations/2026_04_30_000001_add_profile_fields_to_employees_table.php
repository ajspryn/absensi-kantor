<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
     {
          Schema::table('employees', function (Blueprint $table) {
               $table->string('nik_ktp')->nullable()->after('employee_id');
               $table->string('jabatan')->nullable()->after('nik_ktp');
               $table->text('address_ktp')->nullable()->after('address');
               $table->text('address_domisili')->nullable()->after('address_ktp');
               $table->string('mobile')->nullable()->after('phone');
               $table->enum('gender', ['M', 'F'])->nullable()->after('mobile');
               $table->integer('height_cm')->nullable()->after('gender');
               $table->integer('weight_kg')->nullable()->after('height_cm');
               $table->string('hobby')->nullable()->after('weight_kg');
               $table->string('birth_place')->nullable()->after('hobby');
               $table->date('birth_date')->nullable()->after('birth_place');
               $table->string('marital_status')->nullable()->after('birth_date');
               $table->string('residence_status')->nullable()->after('marital_status');
               $table->text('health_condition')->nullable()->after('residence_status');
               $table->text('degenerative_diseases')->nullable()->after('health_condition');
               $table->boolean('has_medical_history')->nullable()->after('degenerative_diseases');

               // Structured / repeatable data stored as json for flexibility
               $table->json('education_history')->nullable()->after('has_medical_history');
               $table->json('training_history')->nullable()->after('education_history');
               $table->json('family_structure')->nullable()->after('training_history');
               $table->json('emergency_contact')->nullable()->after('family_structure');
               $table->text('commitment_notes')->nullable()->after('emergency_contact');
               $table->text('financing_notes')->nullable()->after('commitment_notes');
          });
     }

     public function down(): void
     {
          Schema::table('employees', function (Blueprint $table) {
               $table->dropColumn([
                    'nik_ktp',
                    'jabatan',
                    'address_ktp',
                    'address_domisili',
                    'mobile',
                    'gender',
                    'height_cm',
                    'weight_kg',
                    'hobby',
                    'birth_place',
                    'birth_date',
                    'marital_status',
                    'residence_status',
                    'health_condition',
                    'degenerative_diseases',
                    'has_medical_history',
                    'education_history',
                    'training_history',
                    'family_structure',
                    'emergency_contact',
                    'commitment_notes',
                    'financing_notes',
               ]);
          });
     }
};
