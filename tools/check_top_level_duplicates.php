<?php
$base = getcwd();
$dir = $base . '/database/migrations';
$map = [];
foreach (glob($dir . '/*.php') as $f) {
     $s = file_get_contents($f);
     if (preg_match_all('/Schema::(?:create|table)\s*\(\s*([\'\"])([^\'\"]+)\1/', $s, $m)) {
          foreach ($m[2] as $t) $map[$t][] = basename($f);
     }
}
$dups = [];
foreach ($map as $t => $files) {
     if (count($files) > 1) {
          $dups[$t] = $files;
     }
}
if (empty($dups)) {
     echo "OK: no top-level duplicates\n";
} else {
     foreach ($dups as $t => $files) {
          echo "$t:\n";
          foreach ($files as $file) echo "  - $file\n";
     }
}
