<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sso_clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_id', 64)->unique();
            $table->string('name');
            $table->text('client_secret');
            $table->json('redirect_uris');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sso_authorization_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code_hash', 64)->unique();
            $table->foreignId('sso_client_id')->constrained('sso_clients')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('redirect_uri');
            $table->json('scopes');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['sso_client_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sso_authorization_codes');
        Schema::dropIfExists('sso_clients');
    }
};