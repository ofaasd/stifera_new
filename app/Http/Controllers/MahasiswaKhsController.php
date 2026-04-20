<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MahasiswaKhsController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $tahunAktif = $this->getTahunAktifByTipe((int) ($mahasiswa->tipe_mhs ?? 0));

        $khsAktif = collect();
        $statAktif = [
            'total_sks' => 0,
            'ips' => 0.0,
        ];

        if ($tahunAktif) {
            $khsAktif = $this->getKhsRows($mahasiswa->nim, (int) $tahunAktif->id);
            $statAktif = $this->calculateStat($khsAktif);
        }

        $ipsSemesterIni = (float) ($statAktif['ips'] ?? 0.0);
        $ipkMahasiswa = $this->calculateIpk($mahasiswa->nim);
        $ipsPerSemester = $this->buildIpsPerSemesterSeries($mahasiswa->nim, (int) ($mahasiswa->tipe_mhs ?? 0));

        $riwayatTahun = collect();
        $riwayatKhs = [];

        if ($tahunAktif) {
            $riwayatTahun = $this->getRiwayatTahunAjaran(
                $mahasiswa->nim,
                (int) ($mahasiswa->tipe_mhs ?? 0),
                (int) ($mahasiswa->angkatan ?? 0),
                (int) $tahunAktif->id,
                (int) ($tahunAktif->awal ?? 0)
            );

            foreach ($riwayatTahun as $ta) {
                $rows = $this->getKhsRows($mahasiswa->nim, (int) $ta->id);
                $riwayatKhs[] = [
                    'tahun' => $ta,
                    'rows' => $rows,
                    'stat' => $this->calculateStat($rows),
                ];
            }
        }

        return view('mahasiswa.khs', [
            'title' => 'Kartu Hasil Studi',
            'CurrentPage' => 'content',
            'mahasiswa' => $mahasiswa,
            'tahunAktif' => $tahunAktif,
            'khsAktif' => $khsAktif,
            'statAktif' => $statAktif,
            'ipsSemesterIni' => $ipsSemesterIni,
            'ipkMahasiswa' => $ipkMahasiswa,
            'ipsPerSemester' => $ipsPerSemester,
            'riwayatKhs' => $riwayatKhs,
            'jenisTA' => $this->formatJenisSemester((int) ($tahunAktif->jenis ?? 0)),
        ]);
    }

    private function buildIpsPerSemesterSeries(string $nim, int $tipeMhs): array
    {
        $tahunAjaranList = DB::table('master_nilai as mn')
            ->join('master_tahun_ajaran as mta', 'mta.id', '=', 'mn.id_tahun')
            ->where('mn.nim', $nim)
            ->where('mta.is_delete', '0')
            ->where('mta.tipe_mhs', $tipeMhs)
            ->select('mta.id', 'mta.awal', 'mta.akhir', 'mta.jenis')
            ->distinct()
            ->orderBy('mta.id')
            ->get();

        $labels = [];
        $values = [];

        foreach ($tahunAjaranList as $ta) {
            $rows = $this->getKhsRows($nim, (int) $ta->id);
            if ($rows->isEmpty()) {
                continue;
            }

            $stat = $this->calculateStat($rows);
            $labels[] = ($ta->awal ?? '-') . '/' . ($ta->akhir ?? '-') . ' ' . $this->formatJenisSemester((int) ($ta->jenis ?? 0));
            $values[] = round((float) ($stat['ips'] ?? 0.0), 2);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function calculateIpk(string $nim): float
    {
        $tahunIds = DB::table('master_nilai')
            ->where('nim', $nim)
            ->whereNotNull('id_tahun')
            ->distinct()
            ->pluck('id_tahun');

        $totalSks = 0;
        $totalPoint = 0.0;

        foreach ($tahunIds as $idTahun) {
            $rows = $this->getKhsRows($nim, (int) $idTahun);

            foreach ($rows as $row) {
                $sks = (int) ($row->jumlah_sks ?? 0);
                $nilaiHuruf = $this->resolveNilaiHuruf($row->nhuruf ?? null, $row->nakhir ?? null);
                $point = (float) (function_exists('nbobot') ? nbobot($nilaiHuruf) : 0.0);

                $totalSks += $sks;
                $totalPoint += ($point * $sks);
            }
        }

        if ($totalSks <= 0) {
            return 0.0;
        }

        return $totalPoint / $totalSks;
    }

    private function getKhsRows(string $nim, int $idTahun): Collection
    {
        $rowsTemp = DB::table('master_nilai as mn')
            ->leftJoin('master_jadwal_temp as mjt', function ($join) use ($idTahun) {
                $join->on('mjt.id', '=', 'mn.id_jadwal')
                    ->where('mjt.id_tahun', '=', $idTahun);
            })
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mjt.id_dosen')
            ->select(
                'mn.id',
                'mn.id_tahun',
                'mn.id_jadwal',
                'mn.nim',
                'mn.ntugas',
                'mn.nuts',
                'mn.nuas',
                'mn.nakhir',
                'mn.nhuruf',
                'mn.publish_tugas',
                'mn.publish_uts',
                'mn.publish_uas',
                'mn.validasi_tugas',
                'mn.validasi_uts',
                'mn.validasi_uas',
                'mjt.kode_mata_kuliah',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->where('mn.nim', $nim)
            ->where('mn.id_tahun', $idTahun)
            ->whereNotNull('mjt.id')
            ->orderBy('mn.id')
            ->get();

        if ($rowsTemp->isNotEmpty()) {
            return $rowsTemp;
        }

        return DB::table('master_nilai as mn')
            ->leftJoin('master_jadwal as mj', function ($join) use ($idTahun) {
                $join->on('mj.id_jadwal', '=', 'mn.id_jadwal')
                    ->where('mj.id_tahun', '=', $idTahun);
            })
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mj.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mj.id_dosen')
            ->select(
                'mn.id',
                'mn.id_tahun',
                'mn.id_jadwal',
                'mn.nim',
                'mn.ntugas',
                'mn.nuts',
                'mn.nuas',
                'mn.nakhir',
                'mn.nhuruf',
                'mn.publish_tugas',
                'mn.publish_uts',
                'mn.publish_uas',
                'mn.validasi_tugas',
                'mn.validasi_uts',
                'mn.validasi_uas',
                'mj.kode_mata_kuliah',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->where('mn.nim', $nim)
            ->where('mn.id_tahun', $idTahun)
            ->orderBy('mn.id')
            ->get();
    }

    private function getRiwayatTahunAjaran(string $nim, int $tipeMhs, int $angkatanMahasiswa, int $idTahunAktif, int $tahunAwalAktif): Collection
    {
        $tahunAngkatan = $this->resolveTahunAngkatan($angkatanMahasiswa, $nim);

        $query = DB::table('master_tahun_ajaran')
            ->where('is_delete', '0')
            ->where('tipe_mhs', $tipeMhs)
            ->where('id', '<>', $idTahunAktif)
            ->where('awal', '>=', $tahunAngkatan);

        if ($tahunAwalAktif > 0) {
            $query->where('awal', '<=', $tahunAwalAktif);
        }

        return $query
            ->orderByDesc('id')
            ->get();
    }

    private function resolveTahunAngkatan(int $angkatanMahasiswa, string $nim): int
    {
        if ($angkatanMahasiswa >= 1900) {
            return $angkatanMahasiswa;
        }

        if ($angkatanMahasiswa > 0 && $angkatanMahasiswa < 100) {
            return (int) ('20' . str_pad((string) $angkatanMahasiswa, 2, '0', STR_PAD_LEFT));
        }

        if (strlen($nim) >= 4) {
            $duaDigitAngkatan = substr($nim, 2, 2);
            if (is_numeric($duaDigitAngkatan)) {
                return (int) ('20' . $duaDigitAngkatan);
            }
        }

        return 2000;
    }

    private function getTahunAktifByTipe(int $tipeMhs): ?object
    {
        $tahun = DB::table('master_tahun_ajaran')
            ->where('is_aktif', 1)
            ->where('tipe_mhs', $tipeMhs)
            ->orderByDesc('id')
            ->first();

        if (!$tahun) {
            $tahun = DB::table('master_tahun_ajaran')
                ->where('tipe_mhs', $tipeMhs)
                ->orderByDesc('id')
                ->first();
        }

        return $tahun;
    }

    private function calculateStat(Collection $rows): array
    {
        $totalSks = 0;
        $totalPoint = 0.0;

        foreach ($rows as $row) {
            $sks = (int) ($row->jumlah_sks ?? 0);
            $totalSks += $sks;

            $nilaiHuruf = $this->resolveNilaiHuruf($row->nhuruf ?? null, $row->nakhir ?? null);
            $point = (float) (function_exists('nbobot') ? nbobot($nilaiHuruf) : 0.0);
            $totalPoint += ($point * $sks);
        }

        $ips = $totalSks > 0 ? ($totalPoint / $totalSks) : 0.0;

        return [
            'total_sks' => $totalSks,
            'ips' => $ips,
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

    private function formatJenisSemester(int $jenis): string
    {
        if ($jenis === 1) {
            return 'Ganjil';
        }
        if ($jenis === 2) {
            return 'Genap';
        }
        if ($jenis === 3) {
            return 'Antara Ganjil Genap';
        }
        if ($jenis === 4) {
            return 'Antara Genap Ganjil';
        }

        return '-';
    }
}
