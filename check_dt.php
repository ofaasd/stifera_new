<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$rows = \Illuminate\Support\Facades\DB::table('master_tahun_ajaran')->get();
foreach ($rows as $r) {
    if ($r->tipe_mhs == 2)
        echo "RPL: " . $r->id . ' | ' . $r->id_tahun . ' | ' . $r->awal . '/' . $r->akhir . "\n";
}
