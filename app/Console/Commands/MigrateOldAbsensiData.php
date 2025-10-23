<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;

class MigrateOldAbsensiData extends Command
{
    protected $signature = 'migrate:absensi-old';
    protected $description = 'Migrasi data user dan absensi dari database lama ke database baru';

    public function handle()
    {
        // Koneksi ke database lama
        $oldUsers = DB::connection('mysql_old')->table('user')->get();
        $this->info('Migrasi user...');
        foreach ($oldUsers as $oldUser) {
            $user = User::firstOrCreate([
                'email' => $oldUser->email ?? ($oldUser->username.'@old.com'),
            ], [
                'name' => $oldUser->nama ?? $oldUser->username,
                'password' => Hash::make('password'),
                'role_id' => 2,
                'email_verified_at' => now(),
            ]);
            Employee::firstOrCreate([
                'user_id' => $user->id
            ], [
                'employee_id' => $oldUser->nip ?? $oldUser->id,
                'full_name' => $oldUser->nama ?? $oldUser->username,
                'phone' => $oldUser->no_hp ?? null,
                // 'position' removed; migrate only to position_id when mapping exists
                'hire_date' => $oldUser->tanggal_masuk ?? now(),
                'salary' => $oldUser->gaji ?? 0,
                'department_id' => 1,
                'is_active' => true,
            ]);
        }
        $this->info('Migrasi absensi...');
        $oldAbsensi = DB::connection('mysql_old')->table('absen')->get();
        foreach ($oldAbsensi as $absen) {
            $user = User::where('id', $absen->user_id)->first();
            if ($user) {
                Attendance::firstOrCreate([
                    'user_id' => $user->id,
                    'date' => $absen->tanggal,
                ], [
                    'check_in' => $absen->absen_in,
                    'check_out' => $absen->absen_out,
                    'location_id' => $absen->lokasi_id ?? null,
                    'status_in' => $absen->status_masuk ?? null,
                    'status_out' => $absen->status_pulang ?? null,
                    'notes' => $absen->keterangan ?? null,
                ]);
            }
        }
        $this->info('Migrasi selesai!');
    }
}
