<?php
$base = getcwd();
$dirs = [$base . '/database/migrations', $base . '/database/migrations/archived'];
$map = [];
foreach ($dirs as $dir) {
     if (!is_dir($dir)) continue;
     $files = glob($dir . '/*.php');
     foreach ($files as $f) {
          $s = file_get_contents($f);
          if (preg_match_all('/Schema::(?:create|table)\s*\(\s*(["\'])([^"\']+)\1/', $s, $m)) {
               foreach ($m[2] as $t) {
                    $map[$t][] = substr($f, strlen($base) + 1);
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
