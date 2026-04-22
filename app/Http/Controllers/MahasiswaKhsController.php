<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

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
        $ipkMahasiswa = $this->calculateIpk($mahasiswa->nim, $tahunAktif ? (int) $tahunAktif->id : null);
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
                    'debug' => $this->getKhsDebugQueries($mahasiswa->nim, (int) $ta->id),
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

    public function downloadKhs(?int $idTahun = null)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            abort(403);
        }

        $tahunTarget = $idTahun !== null
            ? $this->getTahunByIdAndTipe($idTahun, (int) ($mahasiswa->tipe_mhs ?? 0))
            : $this->getTahunAktifByTipe((int) ($mahasiswa->tipe_mhs ?? 0));

        if (!$tahunTarget) {
            return redirect()->route('mahasiswa.khs.index')->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $khsRows = $this->getKhsRows($mahasiswa->nim, (int) $tahunTarget->id);
        if ($khsRows->isEmpty()) {
            return redirect()->route('mahasiswa.khs.index')->with('error', 'Data KHS belum tersedia untuk diunduh.');
        }

        $statAktif = $this->calculateStat($khsRows);
        $ipkMahasiswa = $this->calculateIpk($mahasiswa->nim, (int) $tahunTarget->id);
        $mahasiswaProfil = $this->getMahasiswaProfil((int) ($mahasiswa->id ?? 0));
        $pembantuKetuaAkademik = $this->getPembantuKetuaAkademik();

        $html = view('mahasiswa.khs_pdf', [
            'mahasiswa' => $mahasiswa,
            'mahasiswaProfil' => $mahasiswaProfil,
            'tahunAktif' => $tahunTarget,
            'jenisTA' => $this->formatJenisSemester((int) ($tahunTarget->jenis ?? 0)),
            'khsRows' => $khsRows,
            'statAktif' => $statAktif,
            'ipkMahasiswa' => $ipkMahasiswa,
            'pembantuKetuaAkademik' => $pembantuKetuaAkademik,
        ])->render();

        $mpdf = new Mpdf([
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        $mpdf->WriteHTML($html);
        $filename = 'khs_' . ($mahasiswa->nim ?? 'mahasiswa') . '_' . ($tahunTarget->id ?? 'tahun') . '.pdf';

        return response($mpdf->Output($filename, 'D'))
            ->header('Content-Type', 'application/pdf');
    }

    private function getMahasiswaProfil(int $idMahasiswa): ?object
    {
        return DB::table('mahasiswa as m')
            ->leftJoin('program_studi as ps', 'ps.id', '=', 'm.id_program_studi')
            ->leftJoin('master_program_studi as mps', 'mps.id', '=', 'm.id_program_studi')
            ->leftJoin('pegawai_biodata as pbw', 'pbw.id_pegawai', '=', 'm.id_dsn_wali')
            ->select(
                'm.id',
                'm.nim',
                'm.nama',
                DB::raw("COALESCE(NULLIF(CONCAT(COALESCE(ps.jenjang,''), ' / ', COALESCE(ps.nama_jurusan,'')), ' / '), mps.nama_jurusan, '-') as nama_program_studi"),
                DB::raw("CONCAT(COALESCE(pbw.gelar_depan,''), ' ', COALESCE(pbw.nama_lengkap,''), ' ', COALESCE(pbw.gelar_belakang,'')) as dosen_wali")
            )
            ->where('m.id', $idMahasiswa)
            ->first();
    }

    private function getTahunByIdAndTipe(int $idTahun, int $tipeMhs): ?object
    {
        return DB::table('master_tahun_ajaran')
            ->where('id', $idTahun)
            ->where('tipe_mhs', $tipeMhs)
            ->first();
    }

    private function getPembantuKetuaAkademik(): ?string
    {
        $row = DB::table('struktur_pegawai2 as sp2')
            ->leftJoin('pegawai as p', 'p.npp', '=', 'sp2.pembantu_1')
            ->leftJoin('pegawai_biodata as pb', 'pb.id_pegawai', '=', 'p.id')
            ->select(
                DB::raw("TRIM(CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap, p.nama, ''), ' ', COALESCE(pb.gelar_belakang,''))) as nama_gelar")
            )
            ->where('sp2.id', 1)
            ->first();

        $nama = trim((string) ($row->nama_gelar ?? ''));

        return $nama !== '' ? $nama : null;
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

    private function calculateIpk(string $nim, ?int $excludeIdTahun = null): float
    {
        $tahunIds = DB::table('master_nilai')
            ->where('nim', $nim)
            ->whereNotNull('id_tahun')
            ->distinct()
            ->pluck('id_tahun');

        $totalSks = 0;
        $totalPoint = 0.0;

        foreach ($tahunIds as $idTahun) {
            if ($excludeIdTahun !== null && (int) $idTahun === $excludeIdTahun) {
                continue;
            }

            $rows = $this->getKhsRows($nim, (int) $idTahun);

            foreach ($rows as $row) {
                $hasNilai = (trim((string) ($row->nhuruf ?? '')) !== '')
                    || ($row->nakhir !== null && $row->nakhir !== '');
                if (!$hasNilai) {
                    continue;
                }

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
        $rowsFromKrsTemp = $this->buildKhsRowsFromKrsTableQuery('master_krs_temp', $nim, $idTahun)->get();
        if ($rowsFromKrsTemp->isNotEmpty()) {
            return $rowsFromKrsTemp;
        }

        $rowsFromKrsHistory = $this->buildKhsRowsFromKrsTableQuery('master_krs', $nim, $idTahun)->get();
        if ($rowsFromKrsHistory->isNotEmpty()) {
            return $rowsFromKrsHistory;
        }

        return $this->buildKhsRowsFallbackQuery($nim, $idTahun)->get();
    }

    private function getKhsRowsFromKrsTable(string $tableName, string $nim, int $idTahun): Collection
    {
        return $this->buildKhsRowsFromKrsTableQuery($tableName, $nim, $idTahun)->get();
    }

    private function buildKhsRowsFallbackQuery(string $nim, int $idTahun)
    {
        $jadwalTable = $this->resolveJadwalTable($idTahun);
        $jadwalJoinKey = $this->resolveJadwalJoinKey($jadwalTable);

        return DB::table('master_nilai as mn')
            ->leftJoin($jadwalTable . ' as mj', function ($join) use ($idTahun, $jadwalJoinKey) {
                $join->on('mj.' . $jadwalJoinKey, '=', 'mn.id_jadwal')
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
                'mmk.kode_mata_kuliah',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->where('mn.nim', $nim)
            ->where('mn.id_tahun', $idTahun)
            ->orderBy('mn.id');
    }

    private function buildKhsRowsFromKrsTableQuery(string $tableName, string $nim, int $idTahun)
    {
        $jadwalTable = $this->resolveJadwalTable($idTahun);
        $jadwalJoinKey = $this->resolveJadwalJoinKey($jadwalTable);

        return DB::table($tableName . ' as mk')
            ->leftJoin('master_nilai as mn', function ($join) {
                $join->on('mn.nim', '=', 'mk.nim')
                    ->on('mn.id_tahun', '=', 'mk.id_tahun')
                    ->on('mn.id_jadwal', '=', 'mk.id_jadwal');
            })
            ->leftJoin($jadwalTable . ' as mj', function ($join) use ($idTahun, $jadwalJoinKey) {
                $join->on('mj.' . $jadwalJoinKey, '=', 'mk.id_jadwal')
                    ->where('mj.id_tahun', '=', $idTahun);
            })
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mj.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mk.id_dosen')
            ->select(
                'mn.id',
                'mk.id_tahun',
                'mk.id_jadwal',
                'mk.nim',
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
                'mmk.kode_mata_kuliah',
                'mmk.nama_mata_kuliah',
                DB::raw('COALESCE(mmk.jumlah_sks, mk.sks, 0) as jumlah_sks'),
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->where('mk.nim', $nim)
            ->where('mk.id_tahun', $idTahun)
            ->orderBy('mk.id');
    }

    private function getKhsDebugQueries(string $nim, int $idTahun): array
    {
        $tempQuery = $this->buildKhsRowsFromKrsTableQuery('master_krs_temp', $nim, $idTahun);
        $historyQuery = $this->buildKhsRowsFromKrsTableQuery('master_krs', $nim, $idTahun);
        $fallbackQuery = $this->buildKhsRowsFallbackQuery($nim, $idTahun);

        $tempExists = $tempQuery->exists();
        $historyExists = !$tempExists && $historyQuery->exists();

        return [
            'selected_source' => $tempExists ? 'master_krs_temp' : ($historyExists ? 'master_krs' : 'master_nilai'),
            'master_krs_temp' => $this->interpolateQuery($tempQuery),
            'master_krs' => $this->interpolateQuery($historyQuery),
            'master_nilai' => $this->interpolateQuery($fallbackQuery),
        ];
    }

    private function resolveJadwalTable(int $idTahun): string
    {
        $tahunAjaran = DB::table('master_tahun_ajaran')
            ->select('is_aktif')
            ->where('id', $idTahun)
            ->first();

        return (int) ($tahunAjaran->is_aktif ?? 0) === 1 ? 'master_jadwal_temp' : 'master_jadwal';
    }

    private function resolveJadwalJoinKey(string $jadwalTable): string
    {
        return $jadwalTable === 'master_jadwal_temp' ? 'id' : 'id_jadwal';
    }

    private function interpolateQuery($query): string
    {
        $sql = $query->toSql();
        $bindings = $query->getBindings();

        foreach ($bindings as $binding) {
            if ($binding === null) {
                $replacement = 'null';
            } elseif (is_numeric($binding)) {
                $replacement = (string) $binding;
            } else {
                $replacement = "'" . str_replace("'", "''", (string) $binding) . "'";
            }

            $sql = preg_replace('/\?/', $replacement, $sql, 1);
        }

        return $sql;
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
            $hasNilai = (trim((string) ($row->nhuruf ?? '')) !== '')
                || ($row->nakhir !== null && $row->nakhir !== '');
            if (!$hasNilai) {
                continue;
            }

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
