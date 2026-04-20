<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MahasiswaKrsController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $tahunAktif = $this->getTahunAktifByTipe((int) ($mahasiswa->tipe_mhs ?? 0));

        $isKrsDiizinkan = false;
        if ($tahunAktif) {
            $isKrsDiizinkan = DB::table('master_keuangan_mhs')
                ->where('id_mahasiswa', (int) $mahasiswa->id)
                ->where('id_tahun_ajaran', (int) $tahunAktif->id)
                ->where('krs', 1)
                ->exists();
        }

        $krsRows = collect();
        $jadwalTersedia = collect();
        $ipsTerakhir = 0.0;
        $batasSks = 24;

        if ($tahunAktif) {
            $krsRows = $this->getKrsRows($mahasiswa->nim, (int) $tahunAktif->id);
            $jadwalTersedia = $this->getJadwalTersedia($mahasiswa->nim, (int) $tahunAktif->id, (int) ($mahasiswa->tipe_mhs ?? 0));
            $ipsTerakhir = $this->getIpsTerakhir($mahasiswa->nim, (int) $tahunAktif->id);
            $batasSks = function_exists('sksbatas') ? (int) sksbatas($ipsTerakhir) : 24;
        }

        return view('mahasiswa.krs', [
            'title' => 'Kartu Rencana Studi',
            'CurrentPage' => 'content',
            'mahasiswa' => $mahasiswa,
            'tahunAktif' => $tahunAktif,
            'isKrsDiizinkan' => $isKrsDiizinkan,
            'krsRows' => $krsRows,
            'jadwalTersedia' => $jadwalTersedia,
            'totalSks' => (int) $krsRows->sum('sks'),
            'ipsTerakhir' => $ipsTerakhir,
            'batasSks' => $batasSks,
            'jenisTA' => $this->formatJenisSemester((int) ($tahunAktif->jenis ?? 0)),
        ]);
    }

    public function store(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $request->validate([
            'id_jadwal' => ['required', 'integer'],
        ], [
            'id_jadwal.required' => 'Pilih jadwal mata kuliah terlebih dahulu.',
        ]);

        $tahunAktif = $this->getTahunAktifByTipe((int) ($mahasiswa->tipe_mhs ?? 0));
        if (!$tahunAktif) {
            return redirect()->to(url('mhs/krs'))->with('error', 'Tahun ajaran aktif tidak ditemukan.');
        }

        $isKrsDiizinkan = DB::table('master_keuangan_mhs')
            ->where('id_mahasiswa', (int) $mahasiswa->id)
            ->where('id_tahun_ajaran', (int) $tahunAktif->id)
            ->where('krs', 1)
            ->exists();

        if (!$isKrsDiizinkan) {
            return redirect()->to(url('mhs/krs'))->with('error', 'Input KRS belum diizinkan oleh admin keuangan.');
        }

        $idJadwal = (int) $request->input('id_jadwal');

        $jadwal = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->select('mjt.*', 'mmk.jumlah_sks')
            ->where('mjt.id', $idJadwal)
            ->where('mjt.id_tahun', (int) $tahunAktif->id)
            ->where('mjt.status', 1)
            ->where('mjt.tipe_mhs', (int) ($mahasiswa->tipe_mhs ?? 0))
            ->first();

        if (!$jadwal) {
            return redirect()->to(url('mhs/krs'))->with('error', 'Jadwal tidak ditemukan atau tidak aktif.');
        }

        $sudahAda = DB::table('master_krs_temp')
            ->where('nim', $mahasiswa->nim)
            ->where('id_tahun', (int) $tahunAktif->id)
            ->where('id_jadwal', $idJadwal)
            ->exists();

        if ($sudahAda) {
            return redirect()->to(url('mhs/krs'))->with('error', 'Mata kuliah tersebut sudah ada di KRS Anda.');
        }

        $kuotaMaks = (int) ($jadwal->kuota_diambil ?? 0);
        if ($kuotaMaks > 0) {
            $totalDiambil = (int) DB::table('master_krs_temp')
                ->where('id_tahun', (int) $tahunAktif->id)
                ->where('id_jadwal', $idJadwal)
                ->count();

            if ($totalDiambil >= $kuotaMaks) {
                return redirect()->to(url('mhs/krs'))->with('error', 'Kuota mata kuliah tersebut sudah penuh.');
            }
        }

        $ipsTerakhir = $this->getIpsTerakhir($mahasiswa->nim, (int) $tahunAktif->id);
        $batasSks = function_exists('sksbatas') ? (int) sksbatas($ipsTerakhir) : 24;
        $totalSksSaatIni = (int) DB::table('master_krs_temp')
            ->where('nim', $mahasiswa->nim)
            ->where('id_tahun', (int) $tahunAktif->id)
            ->sum('sks');
        $sksBaru = (int) ($jadwal->jumlah_sks ?? 0);

        if (($totalSksSaatIni + $sksBaru) > $batasSks) {
            return redirect()->to(url('mhs/krs'))->with('error', 'Total SKS melebihi batas pengambilan SKS Anda. Batas maksimal semester ini adalah ' . $batasSks . ' SKS.');
        }

        DB::table('master_krs_temp')->insert([
            'id_jadwal' => $idJadwal,
            'id_tahun' => (int) $tahunAktif->id,
            'nim' => $mahasiswa->nim,
            'mata_kuliah' => $jadwal->kode_mata_kuliah,
            'sks' => (int) ($jadwal->jumlah_sks ?? 0),
            'hari' => (string) ($jadwal->hari ?? '-'),
            'sesi' => (string) ($jadwal->sesi ?? '-'),
            'ruang' => (string) ($jadwal->ruang ?? '-'),
            'id_dosen' => (string) ($jadwal->id_dosen ?? ''),
            'kelas' => $jadwal->kelas,
            'is_publish' => 0,
            'log_date' => now(),
        ]);

        return redirect()->to(url('mhs/krs'))->with('status', 'Input KRS berhasil disimpan.');
    }

    public function download(): StreamedResponse
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            abort(403);
        }

        $tahunAktif = $this->getTahunAktifByTipe((int) ($mahasiswa->tipe_mhs ?? 0));
        if (!$tahunAktif) {
            abort(404, 'Tahun ajaran aktif tidak ditemukan.');
        }

        $krsRows = $this->getKrsRows($mahasiswa->nim, (int) $tahunAktif->id);

        $filename = 'krs_' . $mahasiswa->nim . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($krsRows, $mahasiswa, $tahunAktif) {
            $output = fopen('php://output', 'w');

            fputcsv($output, ['NIM', $mahasiswa->nim]);
            fputcsv($output, ['Nama', (string) ($mahasiswa->nama ?? '-')]);
            fputcsv($output, ['Tahun Ajaran', ($tahunAktif->awal ?? '-') . '/' . ($tahunAktif->akhir ?? '-')]);
            fputcsv($output, []);
            fputcsv($output, ['No', 'Kode MK', 'Nama Mata Kuliah', 'SKS', 'Hari', 'Sesi', 'Ruang', 'Dosen']);

            $no = 1;
            foreach ($krsRows as $row) {
                fputcsv($output, [
                    $no,
                    (string) ($row->mata_kuliah ?? '-'),
                    (string) ($row->nama_mata_kuliah ?? $row->mata_kuliah ?? '-'),
                    (int) ($row->sks ?? 0),
                    (string) ($row->hari ?? '-'),
                    (string) ($row->sesi ?? '-'),
                    (string) ($row->ruang ?? '-'),
                    trim((string) ($row->nama_dosen ?? '-')),
                ]);
                $no++;
            }

            fputcsv($output, []);
            fputcsv($output, ['Total SKS', (int) $krsRows->sum('sks')]);

            fclose($output);
        }, 200, $headers);
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

    private function getKrsRows(string $nim, int $idTahun)
    {
        return DB::table('master_krs_temp as mkt')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mkt.mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mkt.id_dosen')
            ->select(
                'mkt.*',
                'mmk.nama_mata_kuliah',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->where('mkt.nim', $nim)
            ->where('mkt.id_tahun', $idTahun)
            ->orderBy('mkt.id')
            ->get();
    }

    private function getJadwalTersedia(string $nim, int $idTahun, int $tipeMhs)
    {
        $sudahDiambil = DB::table('master_krs_temp')
            ->where('nim', $nim)
            ->where('id_tahun', $idTahun)
            ->pluck('id_jadwal');

        $query = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mjt.id_dosen')
            ->leftJoin('master_krs_temp as mkt', function ($join) use ($idTahun) {
                $join->on('mkt.id_jadwal', '=', 'mjt.id')
                    ->where('mkt.id_tahun', '=', $idTahun);
            })
            ->select(
                'mjt.id',
                'mjt.kode_mata_kuliah',
                'mjt.hari',
                'mjt.sesi',
                'mjt.ruang',
                'mjt.kelas',
                'mjt.rombel',
                'mjt.kuota_diambil',
                'mmk.nama_mata_kuliah',
                DB::raw('COALESCE(mmk.jumlah_sks, 0) as jumlah_sks'),
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen"),
                DB::raw('COUNT(mkt.id) as total_diambil')
            )
            ->where('mjt.id_tahun', $idTahun)
            ->where('mjt.status', 1)
            ->where('mjt.tipe_mhs', $tipeMhs)
            ->groupBy(
                'mjt.id',
                'mjt.kode_mata_kuliah',
                'mjt.hari',
                'mjt.sesi',
                'mjt.ruang',
                'mjt.kelas',
                'mjt.rombel',
                'mjt.kuota_diambil',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                'pb.gelar_depan',
                'pb.nama_lengkap',
                'pb.gelar_belakang'
            )
            ->orderBy('mmk.nama_mata_kuliah');

        if ($sudahDiambil->isNotEmpty()) {
            $query->whereNotIn('mjt.id', $sudahDiambil->all());
        }

        return $query->get();
    }

    private function getIpsTerakhir(string $nim, int $idTahunAktif): float
    {
        $tahunTerakhir = DB::table('master_nilai as mn')
            ->join('master_tahun_ajaran as mta', 'mta.id', '=', 'mn.id_tahun')
            ->select('mn.id_tahun')
            ->where('mn.nim', $nim)
            ->where('mn.id_tahun', '<>', $idTahunAktif)
            ->orderByDesc('mta.awal')
            ->orderByDesc('mta.jenis')
            ->orderByDesc('mn.id_tahun')
            ->first();

        if (!$tahunTerakhir) {
            return 0.0;
        }

        $rows = DB::table('master_nilai as mn')
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
            ->select(
                'mn.nhuruf',
                DB::raw('COALESCE(mmk_t.jumlah_sks, mmk.jumlah_sks, 0) as jumlah_sks')
            )
            ->where('mn.nim', $nim)
            ->where('mn.id_tahun', (int) $tahunTerakhir->id_tahun)
            ->get();

        $totalSks = 0;
        $totalPoint = 0.0;
        foreach ($rows as $row) {
            $sks = (int) ($row->jumlah_sks ?? 0);
            $totalSks += $sks;
            $totalPoint += ((float) (function_exists('nbobot') ? nbobot((string) ($row->nhuruf ?? '')) : 0) * $sks);
        }

        return $totalSks > 0 ? ($totalPoint / $totalSks) : 0.0;
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
