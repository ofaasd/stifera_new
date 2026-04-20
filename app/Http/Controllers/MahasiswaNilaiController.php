<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MahasiswaNilaiController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $nilaiRows = $this->getNilaiMahasiswa((string) $mahasiswa->nim);
        $rekap = $this->buildRekapNilai($nilaiRows);

        return view('mahasiswa.daftar_nilai', [
            'title' => 'Daftar Nilai',
            'CurrentPage' => 'content',
            'mahasiswa' => $mahasiswa,
            'nilaiRows' => $nilaiRows,
            'rekap' => $rekap,
        ]);
    }

    private function getNilaiMahasiswa(string $nim): Collection
    {
        return DB::table('master_nilai as mn')
            ->leftJoin('master_jadwal_temp as mjt', function ($join) {
                $join->on('mjt.id', '=', 'mn.id_jadwal')
                    ->on('mjt.id_tahun', '=', 'mn.id_tahun');
            })
            ->leftJoin('master_jadwal as mj', function ($join) {
                $join->on('mj.id_jadwal', '=', 'mn.id_jadwal')
                    ->on('mj.id_tahun', '=', 'mn.id_tahun');
            })
            ->leftJoin('master_mata_kuliah as mmk_t', 'mmk_t.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mj.kode_mata_kuliah')
            ->leftJoin('master_tahun_ajaran as mta', 'mta.id', '=', 'mn.id_tahun')
            ->select(
                'mn.id',
                'mn.id_tahun',
                'mn.id_jadwal',
                'mn.nim',
                'mn.nhuruf',
                'mn.nakhir',
                DB::raw('COALESCE(mjt.kode_mata_kuliah, mj.kode_mata_kuliah) as kode_mata_kuliah'),
                DB::raw('COALESCE(mmk_t.nama_mata_kuliah, mmk.nama_mata_kuliah) as nama_mata_kuliah'),
                DB::raw('COALESCE(mmk_t.jumlah_sks, mmk.jumlah_sks, 0) as jumlah_sks'),
                'mta.awal',
                'mta.akhir',
                'mta.jenis'
            )
            ->where('mn.nim', $nim)
            ->orderBy('mn.id_tahun')
            ->orderBy('mn.id')
            ->get();
    }

    private function buildRekapNilai(Collection $rows): array
    {
        $hurufList = ['E', 'D', 'CD', 'C', 'BC', 'B', 'AB', 'A'];
        $countPerHuruf = array_fill_keys($hurufList, 0);

        $totalSks = 0;
        $totalScore = 0.0;

        foreach ($rows as $row) {
            $huruf = $this->resolveNilaiHuruf($row->nhuruf ?? null, $row->nakhir ?? null);
            $sks = (int) ($row->jumlah_sks ?? 0);

            if (isset($countPerHuruf[$huruf])) {
                $countPerHuruf[$huruf]++;
                $totalSks += $sks;
                $totalScore += ((float) (function_exists('nbobot') ? nbobot($huruf) : 0.0) * $sks);
            }
        }

        $ipk = $totalSks > 0 ? ($totalScore / $totalSks) : 0.0;

        return [
            'count_per_huruf' => $countPerHuruf,
            'total_sks' => $totalSks,
            'total_score' => $totalScore,
            'ipk' => $ipk,
        ];
    }

    private function resolveNilaiHuruf(?string $nilaiHuruf, $nilaiAkhir = null): string
    {
        $huruf = strtoupper(trim((string) ($nilaiHuruf ?? '')));
        if ($huruf !== '') {
            return $huruf;
        }

        if ($nilaiAkhir !== null && $nilaiAkhir !== '' && function_exists('nmutu')) {
            return (string) nmutu((float) $nilaiAkhir);
        }

        return 'E';
    }
}
