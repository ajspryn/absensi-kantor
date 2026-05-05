<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'has_credit_issue')) {
                $table->boolean('has_credit_issue')->nullable()->after('financing_notes');
            }
            if (!Schema::hasColumn('employees', 'credit_institution')) {
                $table->string('credit_institution')->nullable()->after('has_credit_issue');
            }
            if (!Schema::hasColumn('employees', 'credit_plafond')) {
                $table->decimal('credit_plafond', 15, 2)->nullable()->after('credit_institution');
            }
            if (!Schema::hasColumn('employees', 'credit_monthly_installment')) {
                $table->decimal('credit_monthly_installment', 15, 2)->nullable()->after('credit_plafond');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'has_credit_issue',
                'credit_institution',
                'credit_plafond',
                'credit_monthly_installment',
            ]);
        });
    }
};
