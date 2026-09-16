<?php

namespace App\Console\Commands;

use App\Models\SsoClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateSsoClient extends Command
{
    protected $signature = 'sso:client
        {name : Nama aplikasi client}
        {redirect_uris* : Satu atau lebih redirect URI yang sudah didaftarkan}
        {--inactive : Buat client dalam keadaan nonaktif}';

    protected $description = 'Mendaftarkan aplikasi client ke SSO';

    public function handle(): int
    {
        $clientId = Str::random(32);
        $clientSecret = Str::random(64);

        SsoClient::create([
            'client_id' => $clientId,
            'name' => $this->argument('name'),
            'client_secret' => Hash::make($clientSecret),
            'redirect_uris' => $this->argument('redirect_uris'),
            'is_active' => ! $this->option('inactive'),
        ]);

        $this->info('SSO client berhasil dibuat. Simpan secret berikut sekarang karena tidak disimpan dalam bentuk plaintext:');
        $this->line('client_id: '.$clientId);
        $this->line('client_secret: '.$clientSecret);

        return self::SUCCESS;
    }
}