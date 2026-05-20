<?php
$source = file_get_contents('resources/views/employees/complete-profile.blade.php');

// Extract education section
preg_match('/<h6 class="font-14 font-700 mb-2 mt-2 color-highlight">Riwayat Pendidikan Terakhir<\/h6>.*?<button type="button" class="btn btn-full bg-highlight rounded-s shadow-bg shadow-bg-xs mt-3 mb-4" id="add-edu-btn">/s', $source, $matchesEdu);
$eduReplace = str_replace('auth()->user()->employee', '$employee', $matchesEdu[0]) . 'Tambah Riwayat Pendidikan</button>';

// Extract training section
preg_match('/<h6 class="font-14 font-700 mb-2 mt-4 color-highlight">Kursus \/ Training<\/h6>.*?<button type="button" class="btn btn-full bg-highlight rounded-s shadow-bg shadow-bg-xs mt-3 mb-4" id="add-tr-btn">/s', $source, $matchesTr);
$trReplace = str_replace('auth()->user()->employee', '$employee', $matchesTr[0]) . 'Tambah Kursus</button>';

// Extract family section
preg_match('/<h6 class="font-14 font-700 mb-2 mt-2 color-highlight">Susunan Keluarga<\/h6>.*?<button type="button" class="btn btn-full bg-highlight rounded-s shadow-bg shadow-bg-xs mt-3 mb-4" id="add-fam-btn">/s', $source, $matchesFam);
$famReplace = str_replace('auth()->user()->employee', '$employee', $matchesFam[0]) . 'Tambah Anggota Keluarga</button>';

// Extract emergency section
preg_match('/<h6 class="font-14 font-700 mb-2 color-highlight">Kontak Darurat<\/h6>.*?<button type="button" class="btn btn-full bg-highlight rounded-s shadow-bg shadow-bg-xs mt-3 mb-4" id="add-em-btn">/s', $source, $matchesEm);
$emReplace = str_replace('auth()->user()->employee', '$employee', $matchesEm[0]) . 'Tambah Kontak Darurat</button>';

// Extract templates and JS
preg_match('/<template id="education-template">.*<\/script>/is', $source, $matchesJs);
$jsReplace = $matchesJs[0];

// Also grab the CSS
// .stepper-nav down to .repeater-upload-area .upload-title
preg_match('/<style>.*?\.repeater-card.*?<\/style>/is', $source, $matchesCss);
$cssReplace = $matchesCss[0];

file_put_contents('blocks.json', json_encode([
    'edu' => $eduReplace,
    'tr' => $trReplace,
    'fam' => $famReplace,
    'em' => $emReplace,
    'js' => $jsReplace,
    'css' => $cssReplace
]));
echo "Extracted blocks\n";
