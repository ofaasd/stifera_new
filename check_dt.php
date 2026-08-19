<?php
$lines = file('app/Http/Controllers/mahasiswa/YudisiumMahasiswaController.php');
foreach ($lines as $i => $l) {
    if (stripos($l, 'public function store(') !== false) {
        for ($j = $i; $j < $i + 30; $j++) {
            echo ($j + 1) . ': ' . $lines[$j];
        }
        break;
    }
}
