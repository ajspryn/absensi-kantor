<?php
$files = glob(__DIR__ . '/database/migrations/*.php');
$map = [];
foreach ($files as $f) {
     $s = file_get_contents($f);
     if (preg_match_all('/Schema::(?:create|table)\s*\(\s*[\'\"]([^\'\"]+)[\'\"]/', $s, $m)) {
          foreach ($m[1] as $t) {
               $map[$t][] = basename($f);
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
