<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppSetting;

class SettingsController extends Controller
{
     /**
      * Display settings page
      */
     public function index()
     {
          $settingsGroups = [
               'general' => AppSetting::getByGroup('general'),
               'attendance' => AppSetting::getByGroup('attendance'),
               'notifications' => AppSetting::getByGroup('notifications'),
               'security' => AppSetting::getByGroup('security'),
          ];

          return view('admin.settings.index', compact('settingsGroups'));
     }

     /**
      * Update settings
      */
     public function update(Request $request)
     {
          // Validate file uploads
          $request->validate([
               'files.app_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
               'files.app_favicon' => 'nullable|image|mimes:ico,png|max:1024',
          ]);

          $settings = $request->input('settings', []);
          $files = $request->file('files', []);

          // Handle regular settings
          foreach ($settings as $key => $value) {
               $setting = AppSetting::where('key', $key)->first();
               if ($setting) {
                    $setting->setValue($value);
                    $setting->save();
               }
          }

          // Handle file uploads
          foreach ($files as $key => $file) {
               if ($file) {
                    $setting = AppSetting::where('key', $key)->first();
                    if ($setting) {
                         // Delete old file if exists
                         if ($setting->value && file_exists(public_path('storage/' . $setting->value))) {
                              unlink(public_path('storage/' . $setting->value));
                         }

                         // Store new file
                         $directory = $key === 'app_logo' ? 'logos' : 'favicons';
                         $filename = time() . '_' . $file->getClientOriginalName();
                         $path = $file->storeAs($directory, $filename, 'public');

                         $setting->setValue($path);
                         $setting->save();
                    }
               }
          }

          return redirect()->route('admin.settings.index')
               ->with('success', 'Pengaturan berhasil diperbarui.');
     }

     /**
      * Reset settings to default
      */
     public function reset(Request $request)
     {
          $group = $request->input('group');

          if ($group) {
               $this->seedDefaultSettings($group);
          }

          return redirect()->route('admin.settings.index')
               ->with('success', 'Pengaturan ' . ucfirst($group) . ' berhasil direset ke default.');
     }

     /**
      * Seed default settings
      */
     private function seedDefaultSettings($group = null)
     {
          $defaultSettings = [
               // General Settings
               [
                    'key' => 'app_name',
                    'value' => 'Sistem Absensi',
                    'type' => 'text',
                    'group' => 'general',
                    'label' => 'Nama Aplikasi',
                    'description' => 'Nama aplikasi yang ditampilkan di header dan halaman login',
                    'is_public' => true,
               ],
               [
                    'key' => 'company_name',
                    'value' => 'PT. Contoh Perusahaan',
                    'type' => 'text',
                    'group' => 'general',
                    'label' => 'Nama Perusahaan',
                    'description' => 'Nama perusahaan yang ditampilkan di aplikasi',
                    'is_public' => true,
               ],
               [
                    'key' => 'company_address',
                    'value' => 'Jl. Contoh No. 123, Jakarta',
                    'type' => 'text',
                    'group' => 'general',
                    'label' => 'Alamat Perusahaan',
                    'description' => 'Alamat lengkap perusahaan',
                    'is_public' => true,
               ],
               [
                    'key' => 'timezone',
                    'value' => 'Asia/Jakarta',
                    'type' => 'text',
                    'group' => 'general',
                    'label' => 'Zona Waktu',
                    'description' => 'Zona waktu yang digunakan aplikasi',
                    'is_public' => false,
               ],
               [
                    'key' => 'app_logo',
                    'value' => '',
                    'type' => 'file',
                    'group' => 'general',
                    'label' => 'Logo Aplikasi',
                    'description' => 'Logo yang ditampilkan di halaman login dan header aplikasi',
                    'is_public' => true,
               ],
               [
                    'key' => 'app_favicon',
                    'value' => '',
                    'type' => 'file',
                    'group' => 'general',
                    'label' => 'Favicon',
                    'description' => 'Icon kecil yang ditampilkan di tab browser',
                    'is_public' => true,
               ],

               // Attendance Settings
               [
                    'key' => 'work_start_time',
                    'value' => '08:00',
                    'type' => 'text',
                    'group' => 'attendance',
                    'label' => 'Jam Kerja Mulai',
                    'description' => 'Jam mulai kerja standar (format HH:MM)',
                    'is_public' => true,
               ],
               [
                    'key' => 'work_end_time',
                    'value' => '17:00',
                    'type' => 'text',
                    'group' => 'attendance',
                    'label' => 'Jam Kerja Selesai',
                    'description' => 'Jam selesai kerja standar (format HH:MM)',
                    'is_public' => true,
               ],
               [
                    'key' => 'late_tolerance',
                    'value' => '15',
                    'type' => 'number',
                    'group' => 'attendance',
                    'label' => 'Toleransi Terlambat (Menit)',
                    'description' => 'Toleransi keterlambatan dalam menit',
                    'is_public' => true,
               ],
               [
                    'key' => 'enable_location_check',
                    'value' => '1',
                    'type' => 'boolean',
                    'group' => 'attendance',
                    'label' => 'Aktifkan Pengecekan Lokasi',
                    'description' => 'Memerlukan karyawan berada di lokasi kantor saat absen',
                    'is_public' => false,
               ],
               [
                    'key' => 'office_radius',
                    'value' => '100',
                    'type' => 'number',
                    'group' => 'attendance',
                    'label' => 'Radius Kantor (Meter)',
                    'description' => 'Radius dalam meter dari lokasi kantor untuk absensi',
                    'is_public' => false,
               ],

               // Notification Settings
               [
                    'key' => 'enable_email_notifications',
                    'value' => '1',
                    'type' => 'boolean',
                    'group' => 'notifications',
                    'label' => 'Aktifkan Notifikasi Email',
                    'description' => 'Mengirim notifikasi melalui email',
                    'is_public' => false,
               ],
               [
                    'key' => 'admin_email',
                    'value' => 'admin@company.com',
                    'type' => 'text',
                    'group' => 'notifications',
                    'label' => 'Email Admin',
                    'description' => 'Email admin untuk menerima notifikasi',
                    'is_public' => false,
               ],

               // Security Settings
               [
                    'key' => 'password_min_length',
                    'value' => '8',
                    'type' => 'number',
                    'group' => 'security',
                    'label' => 'Panjang Minimum Password',
                    'description' => 'Jumlah karakter minimum untuk password',
                    'is_public' => false,
               ],
               [
                    'key' => 'session_timeout',
                    'value' => '120',
                    'type' => 'number',
                    'group' => 'security',
                    'label' => 'Timeout Session (Menit)',
                    'description' => 'Waktu timeout session dalam menit',
                    'is_public' => false,
               ],
          ];

          foreach ($defaultSettings as $settingData) {
               if ($group && $settingData['group'] !== $group) {
                    continue;
               }

               AppSetting::updateOrCreate(
                    ['key' => $settingData['key']],
                    $settingData
               );
          }
     }
}
