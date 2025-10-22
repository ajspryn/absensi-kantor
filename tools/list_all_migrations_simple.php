<?php
$dirs = [__DIR__ . '/database/migrations', __DIR__ . '/database/migrations/archived'];
$map = [];
foreach ($dirs as $dir) {
     if (!is_dir($dir)) continue;
     $files = glob($dir . '/*.php');
     foreach ($files as $f) {
          $s = file_get_contents($f);
          if (preg_match_all('/Schema::(?:create|table)\s*\(\s*(["\'])([^"\']+)\1/', $s, $m)) {
               foreach ($m[2] as $t) {
                    $map[$t][] = str_replace(__DIR__ . '/', '', $f);
               }
          }
     }
}
ksort($map);
foreach ($map as $table => $files) {
     echo "$table:\n";
     foreach ($files as $file) {
          echo "  - $file\n";
     }
     echo "\n";
}
