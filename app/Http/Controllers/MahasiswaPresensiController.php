<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MahasiswaPresensiController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $nim = (string) ($mahasiswa->nim ?? '');

        $tahunAktif = DB::table('master_tahun_ajaran')
            ->where('is_aktif', 1)
            ->where('tipe_mhs', $mahasiswa->tipe_mhs ?? 1)
            ->first();

        $krsList = $this->getKrsMahasiswa($nim, $tahunAktif?->id ?? null);

        // Collect all id_jadwal to batch-load pertemuan & presensi
        $idJadwalList = $krsList->pluck('id_jadwal')->unique()->toArray();

        $pertemuanMap = $this->getPertemuanMap($idJadwalList);
        $presensiMap  = $this->getPresensiMap($nim, $idJadwalList);
        $activePertemuanMap = $this->getActivePertemuanMap($idJadwalList);

        $presensiData = $this->buildPresensiData($krsList, $pertemuanMap, $presensiMap, $activePertemuanMap);

        return view('mahasiswa.presensi', [
            'title'        => 'Presensi',
            'CurrentPage'  => 'content',
            'mahasiswa'    => $mahasiswa,
            'tahunAktif'   => $tahunAktif,
            'presensiData' => $presensiData,
        ]);
    }

    private function getKrsMahasiswa(string $nim, ?int $idTahun): \Illuminate\Support\Collection
    {
        return DB::table('master_krs_temp as mkt')
            ->select(
                'mkt.id',
                'mkt.id_jadwal',
                'mkt.nim',
                'mjt.kode_mata_kuliah',
                'mjt.hari',
                'mjt.sesi',
                'mjt.ruang',
                'mjt.rombel',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                DB::raw("TRIM(CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,''))) as nama_dosen")
            )
            ->join('master_jadwal_temp as mjt', 'mkt.id_jadwal', '=', 'mjt.id')
            ->join('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'mjt.id_dosen', '=', 'pb.id')
            ->where('mkt.nim', $nim)
            ->when($idTahun, fn ($q) => $q->where('mkt.id_tahun', $idTahun))
            ->orderBy('mmk.nama_mata_kuliah')
            ->get();
    }

    /**
     * Returns array keyed by id_jadwal, value = Collection of pertemuan rows ordered by tgl_pertemuan.
     */
    private function getPertemuanMap(array $idJadwalList): array
    {
        if (empty($idJadwalList)) {
            return [];
        }

        $rows = DB::table('master_pertemuan')
            ->whereIn('id_jadwal', $idJadwalList)
            ->orderBy('tgl_pertemuan')
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $map[$row->id_jadwal][] = $row;
        }
        return $map;
    }

    /**
     * Returns array keyed by "idJadwal_tglPertemuan" => presensi row.
     */
    private function getPresensiMap(string $nim, array $idJadwalList): array
    {
        if (empty($idJadwalList)) {
            return [];
        }

        $rows = DB::table('master_presensi')
            ->where('nim', $nim)
            ->whereIn('id_jadwal', $idJadwalList)
            ->get();

        $map = [];
        foreach ($rows as $row) {
            $tgl = is_string($row->tgl_pertemuan)
                ? $row->tgl_pertemuan
                : (string) $row->tgl_pertemuan;
            $key = $row->id_jadwal . '_' . $tgl;
            $map[$key] = $row;
        }
        return $map;
    }

    /**
     * Returns array keyed by id_jadwal => active pertemuan row (today, unlocked, code not expired).
     */
    private function getActivePertemuanMap(array $idJadwalList): array
    {
        if (empty($idJadwalList)) {
            return [];
        }

        $today = now()->toDateString();
        $now = now();

        $rows = DB::table('master_pertemuan')
            ->whereIn('id_jadwal', $idJadwalList)
            ->whereDate('tgl_pertemuan', $today)
            ->where('kunci_kehadiran', 0)
            ->whereNotNull('kode_kelas')
            ->where('kode_kelas', '<>', '')
            ->where('expired_kode', '>', $now)
            ->orderByDesc('expired_kode')
            ->get();

        $map = [];
        foreach ($rows as $row) {
            if (!isset($map[$row->id_jadwal])) {
                $map[$row->id_jadwal] = $row;
            }
        }

        return $map;
    }

    private function buildPresensiData($krsList, array $pertemuanMap, array $presensiMap, array $activePertemuanMap): array
    {
        $result = [];
        $today = now()->toDateString();

        foreach ($krsList as $krs) {
            $idJadwal   = (int) $krs->id_jadwal;
            $pertemuanList = $pertemuanMap[$idJadwal] ?? [];
            $activePertemuan = $activePertemuanMap[$idJadwal] ?? null;

            $todayKey = $idJadwal . '_' . $today;
            $todayPresensi = $presensiMap[$todayKey] ?? null;
            $sudahHadirHariIni = $todayPresensi && (int) $todayPresensi->status === 1;

            $totalPertemuan = count($pertemuanList);
            $hadir = 0;
            $izin  = 0;
            $alfa  = 0;
            $pertemuanDetails = [];

            foreach ($pertemuanList as $idx => $pertemuan) {
                $tgl = is_string($pertemuan->tgl_pertemuan)
                    ? $pertemuan->tgl_pertemuan
                    : (string) $pertemuan->tgl_pertemuan;

                $key      = $idJadwal . '_' . $tgl;
                $presensi = $presensiMap[$key] ?? null;
                $status   = $presensi ? (int) $presensi->status : null;

                if ($status === 1) {
                    $hadir++;
                } elseif ($status === 2) {
                    $izin++;
                } elseif ($status === 3) {
                    $alfa++;
                }

                $pertemuanDetails[] = [
                    'pertemuan_ke' => $idx + 1,
                    'tanggal'      => $pertemuan->tgl_pertemuan,
                    'status'       => $status,
                ];
            }

            $persenHadir = $totalPertemuan > 0
                ? round(($hadir / $totalPertemuan) * 100, 1)
                : 0;

            $result[] = [
                'krs'              => $krs,
                'total_pertemuan'  => $totalPertemuan,
                'hadir'            => $hadir,
                'izin'             => $izin,
                'alfa'             => $alfa,
                'persen_hadir'     => $persenHadir,
                'pertemuan'        => $pertemuanDetails,
                'active_pertemuan' => $activePertemuan,
                'sudah_hadir_hari_ini' => $sudahHadirHariIni,
                'can_absen'        => $activePertemuan !== null && !$sudahHadirHariIni,
            ];
        }

        return $result;
    }
}
