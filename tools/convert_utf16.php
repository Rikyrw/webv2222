<?php
$files = [
    'app/Http/Controllers/NasabahPulsaController.php',
];
foreach ($files as $f) {
    if (!file_exists($f)) { echo "$f not found\n"; continue; }
    $c = file_get_contents($f);
    $head = substr($c,0,2);
    $b0 = ord($head[0] ?? "\0");
    $b1 = ord($head[1] ?? "\0");
    if ($b0 === 0xFF && $b1 === 0xFE) {
        $u = mb_convert_encoding($c, 'UTF-8', 'UTF-16LE');
        file_put_contents($f, $u);
        echo "Converted $f to UTF-8\n";
    } else {
        echo "No conversion needed for $f\n";
    }
}
