<?php
$files = [
    'app/Http/Controllers/NasabahPulsaController.php',
];
foreach ($files as $f) {
    if (!file_exists($f)) { echo "$f not found\n"; continue; }
    $c = file_get_contents($f);
    if (substr($c,0,3) === "\xEF\xBB\xBF") {
        file_put_contents($f, substr($c,3));
        echo "Stripped UTF-8 BOM from $f\n";
    } else {
        echo "No UTF-8 BOM in $f\n";
    }
}
