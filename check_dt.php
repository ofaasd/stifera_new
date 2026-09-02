<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$rows = \Illuminate\Support\Facades\DB::select("DESCRIBE `mahasiswa`");
$columns = [];
foreach ($rows as $row) {
    $columns[] = $row->Field;
}
print_r(array_intersect(['id_program_studi', 'program_studi', 'prodi'], $columns));
