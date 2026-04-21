<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterPertemuan;
use App\Models\JadwalKr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Manajemen Jadwal';
        $data['CurrentPage'] = 'content';
        $data['activeTab'] = $request->input('active_tab', session('active_tab'));

        $data['matakuliah'] = DB::table('master_mata_kuliah as mmk')
            ->leftJoin('program_studi as ps', 'mmk.id_program_studi', '=', 'ps.id')
            ->select(
                'mmk.kode_mata_kuliah',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                'mmk.semester',
                'ps.nama_jurusan'
            )
            ->where('mmk.is_aktif', 1)
            ->orderBy('mmk.semester')
            ->orderBy('mmk.nama_mata_kuliah')
            ->get();

        $data['jadwalAktif'] = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'mjt.id_dosen', '=', 'pb.id')
            ->leftJoin('master_tahun_ajaran as mta', 'mjt.id_tahun', '=', 'mta.id')
            ->select(
                'mjt.*',
                'mmk.nama_mata_kuliah',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen"),
                'mta.awal',
                'mta.akhir',
                'mta.jenis'
            )
            ->orderByDesc('mjt.id')
            ->limit(300)
            ->get();

        $data['tahunAjarHistory'] = DB::table('master_jadwal as mj')
            ->join('master_tahun_ajaran as mta', 'mj.id_tahun', '=', 'mta.id')
            ->select('mta.id', 'mta.awal', 'mta.akhir', 'mta.jenis')
            ->groupBy('mta.id', 'mta.awal', 'mta.akhir', 'mta.jenis')
            ->orderByDesc('mta.id')
            ->get();

        $selectedHistoryTahun = $request->input('history_tahun');
        $data['selectedHistoryTahun'] = $selectedHistoryTahun;

        $historyQuery = DB::table('master_jadwal as mj')
            ->leftJoin('master_mata_kuliah as mmk', 'mj.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'mj.id_dosen', '=', 'pb.id')
            ->leftJoin('master_tahun_ajaran as mta', 'mj.id_tahun', '=', 'mta.id')
            ->select(
                'mj.*',
                'mmk.nama_mata_kuliah',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen"),
                'mta.awal',
                'mta.akhir',
                'mta.jenis'
            );

        if (!empty($selectedHistoryTahun)) {
            $historyQuery->where('mj.id_tahun', (int) $selectedHistoryTahun);
        }

        $data['jadwalHistory'] = $historyQuery
            ->orderByDesc('mj.id')
            ->limit(300)
            ->get();

        $data['tahunDenganJadwalTemp'] = DB::table('master_jadwal_temp as mjt')
            ->join('master_tahun_ajaran as mta', 'mjt.id_tahun', '=', 'mta.id')
            ->select('mta.id', 'mta.awal', 'mta.akhir', 'mta.jenis')
            ->groupBy('mta.id', 'mta.awal', 'mta.akhir', 'mta.jenis')
            ->orderByDesc('mta.id')
            ->get();

        return view('admin.jadwal.index', $data);
    }

    public function create(string $kodeMataKuliah)
    {
        $mataKuliah = DB::table('master_mata_kuliah')
            ->where('kode_mata_kuliah', $kodeMataKuliah)
            ->first();

        if (!$mataKuliah) {
            return redirect('master/jadwal')->with('error', 'Mata kuliah tidak ditemukan.');
        }

        $tahunAktif = DB::table('master_tahun_ajaran')
            ->where('is_delete', 0)
            ->where('is_aktif', 1)
            ->orderByDesc('id')
            ->first();

        if (!$tahunAktif) {
            $tahunAktif = DB::table('master_tahun_ajaran')->orderByDesc('id')->first();
        }

        if (!$tahunAktif) {
            return redirect('master/jadwal')->with('error', 'Data tahun ajaran belum tersedia.');
        }

        $existingRows = DB::table('master_jadwal_temp')
            ->where('kode_mata_kuliah', $kodeMataKuliah)
            ->where('id_tahun', $tahunAktif->id)
            ->get();

        $existingMap = [];
        foreach ($existingRows as $row) {
            $tipe = ((int) $row->tipe_mhs) === 2 ? 'karyawan' : 'regular';
            $rombel = strtolower((string) ($row->rombel ?? ''));
            $existingMap[$tipe . '_' . $rombel] = $row;
        }

        $data['title'] = 'Input Jadwal';
        $data['CurrentPage'] = 'content';
        $data['mataKuliah'] = $mataKuliah;
        $data['tahunAktif'] = $tahunAktif;
        $data['existingMap'] = $existingMap;
        $data['dosenList'] = DB::table('pegawai_biodata')
            ->where('status_pegawai', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();
        $data['hariList'] = DB::table('master_hari')->orderBy('id')->get();
        $data['sesiList'] = DB::table('master_jam')->orderBy('id')->get();
        $data['ruangList'] = DB::table('master_ruang')->orderBy('nama_ruang')->get();

        return view('admin.jadwal.input', $data);
    }

    public function store(Request $request, string $kodeMataKuliah)
    {
        $tahunAktif = $this->getTahunAktif();

        if (!$tahunAktif) {
            return redirect()->back()->with('error', 'Tahun ajaran aktif belum tersedia.');
        }

        $rows = $request->input('rows', []);
        if (empty($rows)) {
            return redirect()->back()->with('error', 'Tidak ada data jadwal yang dikirim.');
        }

        $preparedRows = $this->prepareRowsForSave($rows, $kodeMataKuliah, (int) $tahunAktif->id);
        if (count($preparedRows) === 0) {
            return redirect()->back()->with('error', 'Belum ada baris jadwal yang lengkap untuk disimpan.');
        }

        $conflicts = array_merge(
            $this->validateInternalConflicts($preparedRows),
            $this->validateDatabaseConflicts($preparedRows)
        );

        if (!empty($conflicts)) {
            return redirect()->back()->with('error', 'Bentrok jadwal ditemukan: ' . implode(' | ', $conflicts));
        }

        $nextKode = (int) DB::table('master_jadwal_temp')
            ->selectRaw('COALESCE(MAX(CAST(kode_jadwal AS UNSIGNED)), 0) as mx')
            ->value('mx');

        $saved = 0;
        foreach ($preparedRows as $row) {
            $payload = [
                'id_dosen' => $row['id_dosen'],
                'id_dosen2' => $row['id_dosen2'],
                'id_tahun' => $row['id_tahun'],
                'kode_mata_kuliah' => $row['kode_mata_kuliah'],
                'hari' => $row['hari'],
                'sesi' => $row['sesi'],
                'ruang' => $row['ruang'],
                'kelas' => null,
                'rombel' => $row['rombel'],
                'kuota_diambil' => $row['kuota_diambil'],
                'status' => $row['status'],
                'tipe_mhs' => $row['tipe_mhs'],
                'rps' => $row['rps'],
                'kp' => $row['kp'],
            ];

            if (!empty($row['existing_id'])) {
                DB::table('master_jadwal_temp')->where('id', $row['existing_id'])->update($payload);
            } else {
                $nextKode++;
                $payload['kode_jadwal'] = (string) $nextKode;
                DB::table('master_jadwal_temp')->insert($payload);
            }
            $saved++;
        }

        return redirect()->back()->with('status', "{$saved} baris jadwal berhasil disimpan.");
    }

    public function edit(string $id)
    {
        $jadwal = DB::table('master_jadwal_temp')->where('id', $id)->first();
        if (!$jadwal) {
            return redirect('master/jadwal')->with('error', 'Data jadwal tidak ditemukan.');
        }

        $mataKuliah = DB::table('master_mata_kuliah')
            ->where('kode_mata_kuliah', $jadwal->kode_mata_kuliah)
            ->first();

        $tahun = DB::table('master_tahun_ajaran')->where('id', $jadwal->id_tahun)->first();

        $data['title'] = 'Edit Jadwal';
        $data['CurrentPage'] = 'content';
        $data['jadwal'] = $jadwal;
        $data['mataKuliah'] = $mataKuliah;
        $data['tahun'] = $tahun;
        $data['dosenList'] = DB::table('pegawai_biodata')
            ->where('status_pegawai', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();
        $data['hariList'] = DB::table('master_hari')->orderBy('id')->get();
        $data['sesiList'] = DB::table('master_jam')->orderBy('id')->get();
        $data['ruangList'] = DB::table('master_ruang')->orderBy('nama_ruang')->get();

        return view('admin.jadwal.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'id_dosen' => 'required|integer',
            'hari' => 'required|string',
            'sesi' => 'required|string',
            'ruang' => 'required|string',
            'kuota_diambil' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
        ]);

        $jadwal = DB::table('master_jadwal_temp')->where('id', $id)->first();
        if (!$jadwal) {
            return redirect('master/jadwal')->with('error', 'Data jadwal tidak ditemukan.');
        }

        $prepared = [[
            'label' => (($jadwal->tipe_mhs == 2) ? 'Karyawan' : 'Reguler') . ' Rombel ' . ($jadwal->rombel ?? '-'),
            'existing_id' => (int) $jadwal->id,
            'id_tahun' => (int) $jadwal->id_tahun,
            'kode_mata_kuliah' => $jadwal->kode_mata_kuliah,
            'id_dosen' => (int) $request->id_dosen,
            'id_dosen2' => !empty($request->id_dosen2) ? (int) $request->id_dosen2 : null,
            'hari' => $request->hari,
            'sesi' => $request->sesi,
            'ruang' => $request->ruang,
            'rombel' => $jadwal->rombel,
            'tipe_mhs' => (int) $jadwal->tipe_mhs,
            'status' => (int) $request->status,
            'kuota_diambil' => (int) $request->input('kuota_diambil', (int) ($jadwal->kuota_diambil ?? 0)),
            'rps' => $jadwal->rps,
            'kp' => $jadwal->kp,
        ]];

        $conflicts = $this->validateDatabaseConflicts($prepared);
        if (!empty($conflicts)) {
            return redirect()->back()->with('error', 'Bentrok jadwal ditemukan: ' . implode(' | ', $conflicts));
        }

        DB::table('master_jadwal_temp')->where('id', $jadwal->id)->update([
            'id_dosen' => (int) $request->id_dosen,
            'id_dosen2' => !empty($request->id_dosen2) ? (int) $request->id_dosen2 : null,
            'hari' => $request->hari,
            'sesi' => $request->sesi,
            'ruang' => $request->ruang,
            'kuota_diambil' => (int) $request->input('kuota_diambil', (int) ($jadwal->kuota_diambil ?? 0)),
            'status' => (int) $request->status,
        ]);

        return redirect('master/jadwal')->with('status', 'Data jadwal berhasil diperbarui.')->with('active_tab', 'daftar-pane');
    }

    public function destroy(string $id)
    {
        $deleted = DB::table('master_jadwal_temp')->where('id', $id)->delete();
        if (!$deleted) {
            return redirect('master/jadwal')->with('error', 'Data jadwal tidak ditemukan.')->with('active_tab', 'daftar-pane');
        }
        return redirect('master/jadwal')->with('status', 'Data jadwal berhasil dihapus.')->with('active_tab', 'daftar-pane');
    }

    public function pindahKeHistory(Request $request)
    {
        $request->validate([
            'id_tahun' => 'required|integer',
        ]);

        $idTahun = (int) $request->id_tahun;
        $rows = DB::table('master_jadwal_temp')->where('id_tahun', $idTahun)->get();
        if ($rows->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada jadwal pada tahun ajaran terpilih.');
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $r) {
                DB::table('master_jadwal')->updateOrInsert(
                    [
                        'id_jadwal' => $r->id,
                        'id_tahun' => $r->id_tahun,
                    ],
                    [
                        'kode_jadwal' => $r->kode_jadwal,
                        'id_dosen' => $r->id_dosen,
                        'id_dosen2' => $r->id_dosen2,
                        'kode_mata_kuliah' => $r->kode_mata_kuliah,
                        'hari' => $r->hari,
                        'sesi' => $r->sesi,
                        'ruang' => $r->ruang,
                        'kelas' => $r->kelas,
                        'kuota_diambil' => $r->kuota_diambil,
                        'status' => $r->status,
                        'rombel' => $r->rombel,
                        'tipe_mhs' => $r->tipe_mhs,
                        'rps' => $r->rps,
                        'kp' => $r->kp,
                    ]
                );
            }

            DB::table('master_jadwal_temp')->whereIn('id', $rows->pluck('id')->all())->delete();
        });

        return redirect('master/jadwal')->with('status', count($rows) . ' jadwal berhasil dipindahkan ke history.')->with('active_tab', 'history-pane');
    }

    public function pertemuanIndex()
    {
        $data['title'] = 'Setting Pertemuan';
        $data['CurrentPage'] = 'content';

        $data['jadwalList'] = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'mjt.id_dosen', '=', 'pb.id')
            ->select(
                'mjt.id',
                'mjt.kode_mata_kuliah',
                'mjt.hari',
                'mjt.sesi',
                'mjt.ruang',
                'mjt.rps',
                'mjt.kp',
                'mjt.rombel',
                'mjt.tipe_mhs',
                'mmk.nama_mata_kuliah',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->orderByDesc('mjt.id')
            ->get();

        return view('admin.jadwal.pertemuan_index', $data);
    }

    public function pertemuanDetail(string $idJadwal)
    {
        $jadwal = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'mjt.id_dosen', '=', 'pb.id')
            ->select(
                'mjt.*',
                'mmk.nama_mata_kuliah',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->where('mjt.id', (int) $idJadwal)
            ->first();

        if (!$jadwal) {
            return redirect('master/pertemuan')->with('error', 'Data jadwal tidak ditemukan.');
        }

        $existing = MasterPertemuan::where('id_jadwal', (int) $idJadwal)
            ->orderBy('id_pertemuan')
            ->get();

        $tanggalByPertemuan = [];
        foreach ($existing as $row) {
            $tanggalByPertemuan[(int) $row->id_pertemuan] = optional($row->tgl_pertemuan)->format('Y-m-d');
        }

        $data['title'] = 'Detail Setting Pertemuan';
        $data['CurrentPage'] = 'content';
        $data['jadwal'] = $jadwal;
        $data['tanggalByPertemuan'] = $tanggalByPertemuan;
        $data['listPertemuan'] = range(1, 16);

        return view('admin.jadwal.pertemuan_detail', $data);
    }

    public function pertemuanSave(Request $request, string $idJadwal)
    {
        $jadwal = DB::table('master_jadwal_temp')->where('id', (int) $idJadwal)->first();
        if (!$jadwal) {
            return redirect('master/pertemuan')->with('error', 'Data jadwal tidak ditemukan.');
        }

        $validated = $request->validate([
            'tanggal' => 'required|array',
            'tanggal.*' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated, $jadwal) {
            for ($i = 1; $i <= 16; $i++) {
                $tanggal = $validated['tanggal'][$i] ?? null;

                if (!empty($tanggal)) {
                    MasterPertemuan::updateOrCreate(
                        [
                            'id_jadwal' => (int) $jadwal->id,
                            'id_tahun' => (int) $jadwal->id_tahun,
                            'id_pertemuan' => $i,
                        ],
                        [
                            'tgl_pertemuan' => $tanggal,
                            'kunci_kehadiran' => 0,
                        ]
                    );
                } else {
                    MasterPertemuan::where('id_jadwal', (int) $jadwal->id)
                        ->where('id_tahun', (int) $jadwal->id_tahun)
                        ->where('id_pertemuan', $i)
                        ->delete();
                }
            }
        });

        return redirect('master/pertemuan/' . (int) $jadwal->id)
            ->with('status', 'Detail pertemuan berhasil disimpan.');
    }

    public function pertemuanUploadDokumen(Request $request, string $idJadwal)
    {
        $jadwal = DB::table('master_jadwal_temp')->where('id', (int) $idJadwal)->first();
        if (!$jadwal) {
            return redirect('master/pertemuan')->with('error', 'Data jadwal tidak ditemukan.');
        }

        $request->validate([
            'rps_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'kp_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if (!$request->hasFile('rps_file') && !$request->hasFile('kp_file')) {
            return redirect('master/pertemuan/' . (int) $jadwal->id)
                ->with('error', 'Silakan pilih file RPS atau KP terlebih dahulu.');
        }

        $updatePayload = [];
        $uploadPath = public_path('assets/files');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if ($request->hasFile('rps_file') && $request->file('rps_file')->isValid()) {
            $rpsFile = $request->file('rps_file');
            $rpsName = 'rps_' . $jadwal->id . '_' . time() . '.' . $rpsFile->getClientOriginalExtension();
            $rpsFile->move($uploadPath, $rpsName);
            $updatePayload['rps'] = $rpsName;
        }

        if ($request->hasFile('kp_file') && $request->file('kp_file')->isValid()) {
            $kpFile = $request->file('kp_file');
            $kpName = 'kp_' . $jadwal->id . '_' . time() . '.' . $kpFile->getClientOriginalExtension();
            $kpFile->move($uploadPath, $kpName);
            $updatePayload['kp'] = $kpName;
        }

        if (!empty($updatePayload)) {
            DB::table('master_jadwal_temp')
                ->where('id', (int) $jadwal->id)
                ->update($updatePayload);
        }

        return redirect('master/pertemuan/' . (int) $jadwal->id)
            ->with('status', 'Dokumen RPS/KP berhasil diupload.');
    }

    private function getTahunAktif(): ?object
    {
        $tahunAktif = DB::table('master_tahun_ajaran')
            ->where('is_delete', 0)
            ->where('is_aktif', 1)
            ->orderByDesc('id')
            ->first();

        if (!$tahunAktif) {
            $tahunAktif = DB::table('master_tahun_ajaran')->orderByDesc('id')->first();
        }

        return $tahunAktif;
    }

    private function prepareRowsForSave(array $rows, string $kodeMataKuliah, int $idTahun): array
    {
        $prepared = [];

        foreach (['regular', 'karyawan'] as $kelasKey) {
            if (!isset($rows[$kelasKey]) || !is_array($rows[$kelasKey])) {
                continue;
            }

            foreach (['a', 'b', 'c'] as $rombelKey) {
                $r = $rows[$kelasKey][$rombelKey] ?? null;
                if (!$r || !is_array($r)) {
                    continue;
                }

                $idDosen = $r['id_dosen'] ?? null;
                $idDosen2 = $r['id_dosen2'] ?? null;
                $hari = $r['hari'] ?? null;
                $sesi = $r['sesi'] ?? null;
                $ruang = $r['ruang'] ?? null;
                $kuotaDiambil = isset($r['kuota_diambil']) ? max(0, (int) $r['kuota_diambil']) : null;
                $status = isset($r['status']) ? (int) $r['status'] : 0;

                if (empty($idDosen) || empty($hari) || empty($sesi) || empty($ruang)) {
                    continue;
                }

                $tipeMhs = $kelasKey === 'karyawan' ? 2 : 1;
                $rombel = strtoupper($rombelKey);
                $existing = DB::table('master_jadwal_temp')
                    ->where('id_tahun', $idTahun)
                    ->where('kode_mata_kuliah', $kodeMataKuliah)
                    ->where('tipe_mhs', $tipeMhs)
                    ->where('rombel', $rombel)
                    ->first();

                $prepared[] = [
                    'label' => ($kelasKey === 'karyawan' ? 'Karyawan' : 'Reguler') . ' Rombel ' . $rombel,
                    'existing_id' => $existing?->id,
                    'id_tahun' => $idTahun,
                    'kode_mata_kuliah' => $kodeMataKuliah,
                    'id_dosen' => (int) $idDosen,
                    'id_dosen2' => !empty($idDosen2) ? (int) $idDosen2 : null,
                    'hari' => $hari,
                    'sesi' => $sesi,
                    'ruang' => $ruang,
                    'rombel' => $rombel,
                    'tipe_mhs' => $tipeMhs,
                    'status' => $status,
                    'kuota_diambil' => $kuotaDiambil ?? (int) ($existing?->kuota_diambil ?? 0),
                    'rps' => $existing?->rps,
                    'kp' => $existing?->kp,
                ];
            }
        }

        return $prepared;
    }

    private function validateInternalConflicts(array $rows): array
    {
        $errors = [];

        for ($i = 0; $i < count($rows); $i++) {
            for ($j = $i + 1; $j < count($rows); $j++) {
                if ($rows[$i]['hari'] !== $rows[$j]['hari'] || $rows[$i]['sesi'] !== $rows[$j]['sesi']) {
                    continue;
                }

                $dosenA = array_filter([$rows[$i]['id_dosen'], $rows[$i]['id_dosen2']]);
                $dosenB = array_filter([$rows[$j]['id_dosen'], $rows[$j]['id_dosen2']]);

                if (count(array_intersect($dosenA, $dosenB)) > 0) {
                    $errors[] = 'Bentrok dosen antar input: ' . $rows[$i]['label'] . ' vs ' . $rows[$j]['label'] . ' (' . $rows[$i]['hari'] . ' / ' . $rows[$i]['sesi'] . ')';
                }

                if ($rows[$i]['ruang'] === $rows[$j]['ruang']) {
                    $errors[] = 'Bentrok ruang antar input: ' . $rows[$i]['label'] . ' vs ' . $rows[$j]['label'] . ' (' . $rows[$i]['hari'] . ' / ' . $rows[$i]['sesi'] . ', ruang ' . $rows[$i]['ruang'] . ')';
                }
            }
        }

        return array_values(array_unique($errors));
    }

    private function validateDatabaseConflicts(array $rows): array
    {
        $errors = [];

        foreach ($rows as $row) {
            $excludeId = $row['existing_id'] ?? 0;

            $dosenIds = array_filter([$row['id_dosen'], $row['id_dosen2']]);
            if (!empty($dosenIds)) {
                $bentrokDosen = DB::table('master_jadwal_temp')
                    ->where('id_tahun', $row['id_tahun'])
                    ->where('hari', $row['hari'])
                    ->where('sesi', $row['sesi'])
                    ->where('id', '!=', $excludeId)
                    ->where(function ($q) use ($dosenIds) {
                        $q->whereIn('id_dosen', $dosenIds)
                            ->orWhereIn('id_dosen2', $dosenIds);
                    })
                    ->first();

                if ($bentrokDosen) {
                    $errors[] = 'Bentrok dosen pada ' . $row['label'] . ' (' . $row['hari'] . ' / ' . $row['sesi'] . ')';
                }
            }

            $bentrokRuang = DB::table('master_jadwal_temp')
                ->where('id_tahun', $row['id_tahun'])
                ->where('hari', $row['hari'])
                ->where('sesi', $row['sesi'])
                ->where('ruang', $row['ruang'])
                ->where('id', '!=', $excludeId)
                ->first();

            if ($bentrokRuang) {
                $errors[] = 'Bentrok ruang pada ' . $row['label'] . ' (' . $row['hari'] . ' / ' . $row['sesi'] . ', ruang ' . $row['ruang'] . ')';
            }
        }

        return array_values(array_unique($errors));
    }

    public function krsIndex()
    {
        $data['title'] = 'Toggle KRS';
        $data['CurrentPage'] = 'content';

        $krsData = JadwalKr::first();
        if (!$krsData) {
            $krsData = JadwalKr::create([
                'id' => 1,
                'status' => 0,
                'upadate_at' => now(),
            ]);
        }

        $data['krs'] = $krsData;
        $data['statusLabel'] = ((int) $krsData->status === 1) ? 'Aktif (Mahasiswa Diijinkan KRS)' : 'Nonaktif (Mahasiswa Tidak Diijinkan KRS)';
        $data['statusClass'] = ((int) $krsData->status === 1) ? 'success' : 'danger';
        $data['nextAction'] = ((int) $krsData->status === 1) ? 'Nonaktifkan' : 'Aktifkan';

        return view('admin.jadwal.krs_index', $data);
    }

    public function krsToggle(Request $request)
    {
        $krsData = JadwalKr::first();
        if (!$krsData) {
            $krsData = JadwalKr::create([
                'id' => 1,
                'status' => 0,
                'upadate_at' => now(),
            ]);
        }

        $newStatus = ((int) $krsData->status === 1) ? 0 : 1;
        $krsData->update([
            'status' => $newStatus,
            'upadate_at' => now(),
        ]);

        $message = $newStatus === 1 ? 'KRS berhasil diaktifkan. Mahasiswa diijinkan untuk input KRS.' : 'KRS berhasil dinonaktifkan. Mahasiswa tidak diijinkan untuk input KRS.';

        return redirect('master/jadwal_krs')->with('status', $message);
    }
}
