<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$idTahun = 21;
$pairs = \Illuminate\Support\Facades\DB::table('tbl_nilai_kuesioner')
    ->where('id_ta', $idTahun)
    ->select('id_jadwal', 'id_dosen')
    ->distinct()
    ->get();
foreach ($pairs as $p) {
    echo $p->id_jadwal . " - " . $p->id_dosen . "\n";
}
