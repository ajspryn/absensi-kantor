<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop individual financing columns and replace with JSON array
            $table->dropColumn([
                'has_credit_issue',
                'credit_institution',
                'credit_plafond',
                'credit_monthly_installment',
            ]);

            // Add JSON column for multiple financing entries
            $table->json('financing_history')->nullable()->after('financing_notes');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('financing_history');

            $table->boolean('has_credit_issue')->nullable();
            $table->string('credit_institution')->nullable();
            $table->decimal('credit_plafond', 15, 2)->nullable();
            $table->decimal('credit_monthly_installment', 15, 2)->nullable();
        });
    }
};
