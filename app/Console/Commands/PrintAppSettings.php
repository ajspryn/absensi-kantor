<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AppSetting;

class PrintAppSettings extends Command
{
     protected $signature = 'print:app-settings';
     protected $description = 'Print app logo and favicon settings and file existence';

     public function handle()
     {
          $keys = ['app_logo', 'app_favicon'];
          foreach (AppSetting::whereIn('key', $keys)->get() as $s) {
               $val = $s->value ?: '(empty)';
               $path = public_path('storage/' . $s->value);
               $exists = file_exists($path) ? 'exists' : 'missing';
               $mtime = $exists === 'exists' ? date('Y-m-d H:i:s', filemtime($path)) : '-';
               $this->info("{$s->key} => {$val} ({$exists}, mtime: {$mtime})");
          }

          $this->info('\nFiles in storage/app/public/logos:');
          $logos = glob(storage_path('app/public/logos/*')) ?: [];
          foreach ($logos as $f) {
               $this->line(basename($f) . ' - ' . date('Y-m-d H:i:s', filemtime($f)));
          }

          $this->info('\nFiles in storage/app/public/favicons:');
          $favs = glob(storage_path('app/public/favicons/*')) ?: [];
          foreach ($favs as $f) {
               $this->line(basename($f) . ' - ' . date('Y-m-d H:i:s', filemtime($f)));
          }

          return 0;
     }
}
