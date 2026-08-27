<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('master_jadwal_temp');
foreach ($columns as $c) {
    if ($c == 'id_dosen2' || $c == 'id_dosen') {
        echo $c . " type: " . \Illuminate\Support\Facades\Schema::getColumnType('master_jadwal_temp', $c) . "\n";
    }
}
