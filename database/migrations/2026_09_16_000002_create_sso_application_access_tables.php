<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sso_application_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sso_client_id')->constrained('sso_clients')->cascadeOnDelete();
            $table->string('code', 100);
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['sso_client_id', 'code']);
        });

        Schema::create('sso_user_application_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sso_client_id')->constrained('sso_clients')->cascadeOnDelete();
            $table->foreignId('sso_application_role_id')->constrained('sso_application_roles')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'sso_client_id']);
            $table->index(['sso_client_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sso_user_application_access');
        Schema::dropIfExists('sso_application_roles');
    }
};