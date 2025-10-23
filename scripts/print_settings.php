<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AppSetting;

$keys = ['app_logo', 'app_favicon'];
foreach (AppSetting::whereIn('key', $keys)->get() as $s) {
     $val = $s->value ?: '(empty)';
     $path = public_path('storage/' . $s->value);
     $exists = file_exists($path) ? 'exists' : 'missing';
     $mtime = $exists === 'exists' ? date('Y-m-d H:i:s', filemtime($path)) : '-';
     echo "{$s->key} => {$val} ({$exists}, mtime: {$mtime})\n";
}

echo "\nFiles in storage/app/public/logos:\n";
$logos = glob(__DIR__ . '/storage/app/public/logos/*');
if ($logos) {
     foreach ($logos as $f) {
          echo basename($f) . ' - ' . date('Y-m-d H:i:s', filemtime($f)) . "\n";
     }
} else {
     echo "(none)\n";
}

echo "\nFiles in storage/app/public/favicons:\n";
$favs = glob(__DIR__ . '/storage/app/public/favicons/*');
if ($favs) {
     foreach ($favs as $f) {
          echo basename($f) . ' - ' . date('Y-m-d H:i:s', filemtime($f)) . "\n";
     }
} else {
     echo "(none)\n";
}
