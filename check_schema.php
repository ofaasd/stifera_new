<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$result = \Illuminate\Support\Facades\DB::select('SHOW COLUMNS FROM pegawai_penelitian WHERE FIELD IN ("dokumen", "proposal", "lap_kemajuan", "lap_keuangan", "lap_akhir")');

foreach($result as $r) {
    echo $r->Field . ': ' . $r->Type . "\n";
}
