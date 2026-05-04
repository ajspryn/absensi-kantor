<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('ktp_path')->nullable()->after('nik_ktp');
            $table->string('kk_path')->nullable()->after('ktp_path');
            $table->string('marriage_certificate_path')->nullable()->after('marital_status');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['ktp_path', 'kk_path', 'marriage_certificate_path']);
        });
    }
};
