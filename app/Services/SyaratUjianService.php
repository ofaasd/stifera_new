<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SyaratUjianService
{
    /**
     * Cek status kelayakan ujian per mahasiswa.
     *
     * @param string $nim
     * @param int $idTahun
     * @param string $jenis ('uts' atau 'uas')
     * @return array [
     *     'is_lolos' => bool,
     *     'syarat_dosen_wali' => bool,
     *     'syarat_kepala_bak' => bool,
     *     'keterangan' => string
     * ]
     */
    public static function cekKelayakan(string $nim, int $idTahun, string $jenis = 'uts'): array
    {
        $batch = self::cekKelayakanBatch([$nim], $idTahun, $jenis);
        return $batch[$nim] ?? [
            'is_lolos' => false,
            'syarat_dosen_wali' => false,
            'syarat_kepala_bak' => false,
            'keterangan' => 'Belum Validasi Dosen Wali & Kepala BAK',
        ];
    }

    /**
     * Cek status kelayakan ujian secara batch untuk banyak mahasiswa (optimasi query).
     *
     * @param array $nims
     * @param int $idTahun
     * @param string $jenis ('uts' atau 'uas')
     * @return array [ nim => [ 'is_lolos' => bool, ... ] ]
     */
    public static function cekKelayakanBatch(array $nims, int $idTahun, string $jenis = 'uts'): array
    {
        $cleanNims = collect($nims)
            ->map(fn($n) => trim((string) $n))
            ->filter(fn($n) => $n !== '')
            ->unique()
            ->values();

        if ($cleanNims->isEmpty()) {
            return [];
        }

        $jenisLower = strtolower($jenis) === 'uas' ? 'uas' : 'uts';

        // 1. Cek Validasi Dosen Wali (KRS temp: is_publish == 1)
        $krsData = DB::table('master_krs_temp')
            ->whereIn('nim', $cleanNims->all())
            ->where('id_tahun', $idTahun)
            ->groupBy('nim')
            ->select('nim', DB::raw('MAX(is_publish) as max_publish'))
            ->pluck('max_publish', 'nim');

        // 2. Cek Validasi Kepala BAK / Keuangan (master_keuangan_mhs: uts == 1 atau uas == 1)
        $keuanganData = DB::table('mahasiswa as m')
            ->join('master_keuangan_mhs as km', function ($join) use ($idTahun) {
                $join->on('km.id_mahasiswa', '=', 'm.id')
                    ->where('km.id_tahun_ajaran', '=', $idTahun);
            })
            ->whereIn('m.nim', $cleanNims->all())
            ->select('m.nim', 'km.uts', 'km.uas')
            ->get()
            ->keyBy('nim');

        $results = [];

        foreach ($cleanNims as $nim) {
            $maxPublish = $krsData->get($nim);
            // Mahasiswa dianggap lolos syarat dosen wali jika KRS nya disetujui (is_publish == 1)
            $syaratDosenWali = !is_null($maxPublish) && ((int) $maxPublish === 1);

            $keu = $keuanganData->get($nim);
            $syaratBak = false;
            if ($keu) {
                if ($jenisLower === 'uts') {
                    $syaratBak = ((int) ($keu->uts ?? 0)) === 1;
                } else {
                    $syaratBak = ((int) ($keu->uas ?? 0)) === 1;
                }
            }

            $isLolos = $syaratDosenWali && $syaratBak;

            if ($isLolos) {
                $keterangan = 'Lolos Validasi';
            } elseif (!$syaratDosenWali && !$syaratBak) {
                $keterangan = 'Belum Validasi Dosen Wali & Kepala BAK';
            } elseif (!$syaratDosenWali) {
                $keterangan = 'Belum Validasi Dosen Wali';
            } else {
                $keterangan = 'Belum Validasi Kepala BAK';
            }

            $results[$nim] = [
                'is_lolos' => $isLolos,
                'syarat_dosen_wali' => $syaratDosenWali,
                'syarat_kepala_bak' => $syaratBak,
                'keterangan' => $keterangan,
            ];
        }

        return $results;
    }
}
