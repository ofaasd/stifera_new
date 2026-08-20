<?php
$c = file_get_contents('resources/views/admin/ijazah/cetak_transkrip_depan.blade.php');
$c = str_replace(
    "\$dokumen->mahasiswa->yudisiumBerkas()->where('jenis_berkas','skripsi')->first()->nama_berkas ?? 'HUBUNGAN DUKUNGAN KELUARGA DENGAN KEPATUHAN KONSUMSI OBAT PASIEN'",
    "'HUBUNGAN DUKUNGAN KELUARGA DENGAN KEPATUHAN KONSUMSI OBAT PASIEN'",
    $c
);
file_put_contents('resources/views/admin/ijazah/cetak_transkrip_depan.blade.php', $c);
echo "Fixed undefined method";
