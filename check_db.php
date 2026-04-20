<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$files = glob('storage/app/public/pegawai/penelitian/dokumen/*');
echo "Files in storage/app/public/pegawai/penelitian/dokumen: " . count($files) . "\n";
if (!empty($files)) {
    echo "Sample: " . basename($files[0]) . "\n\n";
}

echo "Latest 3 DB records:\n";
$data = \App\Models\PegawaiPenelitian::orderByDesc('id')->limit(3)->select('id', 'dokumen')->get();

foreach ($data as $row) {
    echo "ID: " . $row->id . " | dokumen: " . substr($row->dokumen ?: 'NULL', 0, 50) . "\n";
}
