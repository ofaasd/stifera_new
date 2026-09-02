<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$rows = \Illuminate\Support\Facades\DB::table('master_tahun_ajaran')->where('tipe_mhs', 1)->orderByDesc('id')->first();
var_dump($rows->id);
