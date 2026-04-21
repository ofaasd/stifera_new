<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanUjianController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Pengaturan Ujian';
        $data['CurrentPage'] = 'content';
        $data['activeTab'] = $request->input('active_tab', session('active_tab'));

        $tahunReguler = $this->getTahunAktifByTipe(1);
        $tahunRpl = $this->getTahunAktifByTipe(2);

        $data['tahunReguler'] = $tahunReguler;
        $data['tahunRpl'] = $tahunRpl;

        $data['jadwalReguler'] = $tahunReguler
            ? $this->getListJadwalByTahun((int) $tahunReguler->id)
            : collect();

        $data['jadwalRpl'] = $tahunRpl
            ? $this->getListJadwalByTahun((int) $tahunRpl->id)
            : collect();

        return view('admin.ujian.index', $data);
    }

    public function detail(string $id_jadwal)
    {
        $jadwalId = (int) $id_jadwal;

        $jadwal = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->leftJoin('master_tahun_ajaran as mta', 'mta.id', '=', 'mjt.id_tahun')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mjt.id_dosen')
            ->select(
                'mjt.*',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                'mmk.tp',
                'mta.awal',
                'mta.akhir',
                'mta.jenis',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->where('mjt.id', $jadwalId)
            ->first();

        if (!$jadwal) {
            return redirect('master/pengaturan-ujian')->with('error', 'Data jadwal tidak ditemukan.');
        }

        $pengaturan = DB::table('tbl_jadwal_ujian')
            ->where('id_jadwal', $jadwalId)
            ->where('ta', $jadwal->id_tahun)
            ->first();

        $data['title'] = 'Detail Pengaturan Ujian';
        $data['CurrentPage'] = 'content';
        $data['jadwal'] = $jadwal;
        $data['pengaturan'] = $pengaturan;
        $data['jamList'] = DB::table('master_jam')->orderBy('id')->get();

        return view('admin.ujian.detail', $data);
    }

    public function save(Request $request, string $id_jadwal)
    {
        $jadwalId = (int) $id_jadwal;

        $jadwal = DB::table('master_jadwal_temp')->where('id', $jadwalId)->first();
        if (!$jadwal) {
            return redirect('master/pengaturan-ujian')->with('error', 'Data jadwal tidak ditemukan.');
        }

        $validated = $request->validate([
            'tanggal_uts_t' => ['nullable', 'date'],
            'id_jam_uts_t' => ['nullable', 'integer'],
            'tanggal_uas_t' => ['nullable', 'date'],
            'id_jam_uas_t' => ['nullable', 'integer'],
            'tanggal_uts_p' => ['nullable', 'date'],
            'id_jam_uts_p' => ['nullable', 'integer'],
            'tanggal_uas_p' => ['nullable', 'date'],
            'id_jam_uas_p' => ['nullable', 'integer'],
        ]);

        DB::table('tbl_jadwal_ujian')->updateOrInsert(
            [
                'id_jadwal' => $jadwalId,
                'ta' => (int) $jadwal->id_tahun,
            ],
            [
                'tanggal_uts_t' => $validated['tanggal_uts_t'] ?? null,
                'id_jam_uts_t' => $validated['id_jam_uts_t'] ?? null,
                'tanggal_uas_t' => $validated['tanggal_uas_t'] ?? null,
                'id_jam_uas_t' => $validated['id_jam_uas_t'] ?? null,
                'tanggal_uts_p' => $validated['tanggal_uts_p'] ?? null,
                'id_jam_uts_p' => $validated['id_jam_uts_p'] ?? null,
                'tanggal_uas_p' => $validated['tanggal_uas_p'] ?? null,
                'id_jam_uas_p' => $validated['id_jam_uas_p'] ?? null,
                'log' => now(),
            ]
        );

        return redirect('master/pengaturan-ujian/detail/' . $jadwalId)
            ->with('status', 'Pengaturan ujian berhasil disimpan.');
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

    private function getListJadwalByTahun(int $idTahun)
    {
        return DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mjt.id_dosen')
            ->leftJoin('tbl_jadwal_ujian as tju', function ($join) {
                $join->on('tju.id_jadwal', '=', 'mjt.id')
                    ->on('tju.ta', '=', 'mjt.id_tahun');
            })
            ->select(
                'mjt.id',
                'mjt.id_tahun',
                'mjt.kode_mata_kuliah',
                'mjt.hari',
                'mjt.sesi',
                'mjt.ruang',
                'mjt.rombel',
                'mjt.tipe_mhs',
                'mmk.nama_mata_kuliah',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen"),
                DB::raw('CASE WHEN tju.id IS NULL THEN 0 ELSE 1 END as sudah_diatur')
            )
            ->where('mjt.id_tahun', $idTahun)
            ->orderBy('mjt.kode_mata_kuliah')
            ->orderBy('mjt.rombel')
            ->get();
    }
}
