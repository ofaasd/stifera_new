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

    public function kursi(string $id_jadwal)
    {
        $jadwalId = (int) $id_jadwal;

        $jadwal = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->select('mjt.*', 'mmk.nama_mata_kuliah')
            ->where('mjt.id', $jadwalId)
            ->first();

        if (!$jadwal) {
            return redirect('master/pengaturan-ujian')->with('error', 'Data jadwal tidak ditemukan.');
        }

        $peserta = DB::table('master_krs_temp as mk')
            ->leftJoin('mahasiswa as m', 'm.nim', '=', 'mk.nim')
            ->leftJoin('tbl_tempat_ujian as ttu', function ($join) {
                $join->on('ttu.id_jadwal', '=', 'mk.id_jadwal')
                    ->on('ttu.ta', '=', 'mk.id_tahun')
                    ->on('ttu.nim', '=', 'mk.nim');
            })
            ->select(
                'mk.nim',
                'm.nama as nama_mahasiswa',
                'ttu.no_kursi',
                'ttu.ruang'
            )
            ->where('mk.id_jadwal', $jadwalId)
            ->where('mk.id_tahun', (int) $jadwal->id_tahun)
            ->orderBy('mk.nim')
            ->get()
            ->values();

        if ($peserta->isEmpty()) {
            return redirect('master/pengaturan-ujian/detail/' . $jadwalId)
                ->with('error', 'Belum ada mahasiswa pada jadwal ini.');
        }

        $existingRuang = DB::table('tbl_tempat_ujian')
            ->where('id_jadwal', $jadwalId)
            ->where('ta', (int) $jadwal->id_tahun)
            ->value('ruang');

        $data['title'] = 'Pengaturan Nomor Kursi Ujian';
        $data['CurrentPage'] = 'content';
        $data['jadwal'] = $jadwal;
        $data['peserta'] = $peserta;
        $data['ruangList'] = DB::table('master_ruang')->orderBy('nama_ruang')->get();
        $data['selectedRuang'] = trim((string) ($existingRuang ?: ($jadwal->ruang ?? '-')));
        $data['maxKursi'] = (int) $peserta->count();
        $data['isKursiRuangLengkap'] = !$peserta->contains(function ($row) {
            return trim((string) ($row->ruang ?? '')) === ''
                || $row->no_kursi === null
                || trim((string) ($row->no_kursi ?? '')) === '';
        });

        return view('admin.ujian.kursi', $data);
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

        DB::transaction(function () use ($jadwalId, $jadwal, $validated) {
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

            $this->syncNomorKursiByNim($jadwalId, (int) $jadwal->id_tahun, (string) ($jadwal->ruang ?? '-'));
        });

        return redirect('master/pengaturan-ujian/detail/' . $jadwalId)
            ->with('status', 'Pengaturan ujian berhasil disimpan.');
    }

    public function saveKursi(Request $request, string $id_jadwal)
    {
        $jadwalId = (int) $id_jadwal;

        $jadwal = DB::table('master_jadwal_temp')->where('id', $jadwalId)->first();
        if (!$jadwal) {
            return redirect('master/pengaturan-ujian')->with('error', 'Data jadwal tidak ditemukan.');
        }

        $pesertaNim = DB::table('master_krs_temp')
            ->where('id_jadwal', $jadwalId)
            ->where('id_tahun', (int) $jadwal->id_tahun)
            ->orderBy('nim')
            ->pluck('nim')
            ->filter(fn ($nim) => trim((string) $nim) !== '')
            ->values();

        if ($pesertaNim->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada mahasiswa pada jadwal ini.');
        }

        $maxKursi = (int) $pesertaNim->count();

        $validated = $request->validate([
            'ruang_ujian' => ['required', 'string', 'max:100'],
            'no_kursi' => ['required', 'array'],
            'no_kursi.*' => ['required', 'integer', 'min:1'],
        ]);

        $nimKeys = collect(array_keys($validated['no_kursi'] ?? []));
        $isKeyValid = $nimKeys->diff($pesertaNim)->isEmpty();
        if (!$isKeyValid) {
            return redirect()->back()->with('error', 'Data nomor kursi tidak valid untuk daftar peserta jadwal ini.');
        }

        $kursiList = collect($validated['no_kursi'])
            ->map(fn ($v) => (int) $v)
            ->values();

        if ($kursiList->contains(fn ($v) => $v > $maxKursi)) {
            return redirect()->back()->with('error', 'Nomor kursi melebihi jumlah peserta.');
        }

        if ($kursiList->count() !== $kursiList->unique()->count()) {
            return redirect()->back()->with('error', 'Nomor kursi tidak boleh duplikat.');
        }

        $ruang = trim((string) ($validated['ruang_ujian'] ?? '-'));

        DB::transaction(function () use ($jadwalId, $jadwal, $pesertaNim, $validated, $ruang) {
            DB::table('tbl_tempat_ujian')
                ->where('id_jadwal', $jadwalId)
                ->where('ta', (int) $jadwal->id_tahun)
                ->whereNotIn('nim', $pesertaNim->all())
                ->delete();

            foreach ($pesertaNim as $index => $nim) {
                $noKursi = (int) ($validated['no_kursi'][(string) $nim] ?? ($index + 1));

                DB::table('tbl_tempat_ujian')->updateOrInsert(
                    [
                        'id_jadwal' => $jadwalId,
                        'ta' => (int) $jadwal->id_tahun,
                        'nim' => (string) $nim,
                    ],
                    [
                        'no_kursi' => $noKursi,
                        'ruang' => $ruang,
                        'log' => now(),
                    ]
                );
            }
        });

        return redirect('master/pengaturan-ujian/kursi/' . $jadwalId)
            ->with('status', 'Pengaturan ruang dan nomor kursi berhasil disimpan.');
    }

    private function syncNomorKursiByNim(int $idJadwal, int $idTahun, string $ruang): void
    {
        $pesertaNim = DB::table('master_krs_temp')
            ->where('id_jadwal', $idJadwal)
            ->where('id_tahun', $idTahun)
            ->orderBy('nim')
            ->pluck('nim')
            ->filter(fn ($nim) => trim((string) $nim) !== '')
            ->values();

        if ($pesertaNim->isEmpty()) {
            DB::table('tbl_tempat_ujian')
                ->where('id_jadwal', $idJadwal)
                ->where('ta', $idTahun)
                ->delete();
            return;
        }

        DB::table('tbl_tempat_ujian')
            ->where('id_jadwal', $idJadwal)
            ->where('ta', $idTahun)
            ->whereNotIn('nim', $pesertaNim->all())
            ->delete();

        foreach ($pesertaNim as $index => $nim) {
            DB::table('tbl_tempat_ujian')->updateOrInsert(
                [
                    'id_jadwal' => $idJadwal,
                    'ta' => $idTahun,
                    'nim' => (string) $nim,
                ],
                [
                    'no_kursi' => $index + 1,
                    'ruang' => $ruang,
                    'log' => now(),
                ]
            );
        }
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
