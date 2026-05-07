<?php
$files = [
    'app/Http/Controllers/NasabahPulsaController.php',
    'app/Http/Controllers/NasabahPlnController.php',
    'resources/views/nasabah/pulsa.blade.php',
    'resources/views/nasabah/pln.blade.php',
];
foreach ($files as $f) {
    if (!file_exists($f)) {
        echo "$f: not found\n";
        continue;
    }
    $c = file_get_contents($f);
    $b = substr($c, 0, 4);
    $hex = '';
    for ($i = 0; $i < strlen($b); $i++) {
        $hex .= sprintf('%02X ', ord($b[$i]));
    }
    echo "$f: BOM bytes => $hex\n";
}

echo "\n(0xEF 0xBB 0xBF = UTF-8 BOM; 0xFF 0xFE/0xFE 0xFF = UTF-16 BOM)\n";
