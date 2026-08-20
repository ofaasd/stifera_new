<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$ps = \Illuminate\Support\Facades\DB::table('program_studi')->get();
foreach ($ps as $p)
    echo $p->id . ' - ' . $p->jenjang . ' - ' . $p->nama_jurusan . "\n";
