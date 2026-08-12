<?php

namespace App\Http\Controllers;

use App\Models\MasterPertemuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mpdf\Mpdf;

class PegawaiPertemuanController extends Controller
{
    /**
     * Resolve ID baris di tabel pegawai_biodata dari id_pegawai (id di tabel pegawai).
     * Kolom master_jadwal_temp.id_dosen merujuk ke pegawai_biodata.id (bukan id_pegawai).
     */
    private function getBiodataId(int $idPegawai): ?int
    {
        $row = DB::table('pegawai_biodata')
            ->where('id_pegawai', $idPegawai)
            ->select('id')
            ->first();

        return $row ? (int) $row->id : null;
    }

    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $biodataId = $this->getBiodataId((int) $pegawai->id);

        $jadwalList = DB::table('master_jadwal_temp as mjt')
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
            ->where('mjt.id_dosen', $biodataId)
            ->orderByDesc('mjt.id')
            ->get();

        return view('pegawai.pertemuan.index', [
            'title' => 'Pengaturan Pertemuan',
            'CurrentPage' => 'content',
            'jadwalList' => $jadwalList,
        ]);
    }

    public function detail(string $idJadwal)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $biodataId = $this->getBiodataId((int) $pegawai->id);

        $jadwal = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'mjt.id_dosen', '=', 'pb.id')
            ->select(
                'mjt.*',
                'mmk.nama_mata_kuliah',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->where('mjt.id', (int) $idJadwal)
            ->where('mjt.id_dosen', $biodataId)   // keamanan: hanya jadwal milik dosen ini
            ->first();

        if (!$jadwal) {
            return redirect('dosen/pertemuan')->with('error', 'Data jadwal tidak ditemukan atau bukan milik Anda.');
        }

        $existing = MasterPertemuan::where('id_jadwal', (int) $idJadwal)
            ->orderBy('id_pertemuan')
            ->get();

        $tanggalByPertemuan = [];
        $pertemuanByNomor = [];
        foreach ($existing as $row) {
            $tanggalByPertemuan[(int) $row->id_pertemuan] = optional($row->tgl_pertemuan)->format('Y-m-d');
            $pertemuanByNomor[(int) $row->id_pertemuan] = $row;
        }

        return view('pegawai.pertemuan.detail', [
            'title' => 'Detail Setting Pertemuan',
            'CurrentPage' => 'content',
            'jadwal' => $jadwal,
            'tanggalByPertemuan' => $tanggalByPertemuan,
            'pertemuanByNomor' => $pertemuanByNomor,
            'listPertemuan' => range(1, 16),
        ]);
    }

    public function save(Request $request, string $idJadwal)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $biodataId = $this->getBiodataId((int) $pegawai->id);

        $jadwal = DB::table('master_jadwal_temp')
            ->where('id', (int) $idJadwal)
            ->where('id_dosen', $biodataId)
            ->first();

        if (!$jadwal) {
            return redirect('dosen/pertemuan')->with('error', 'Data jadwal tidak ditemukan atau bukan milik Anda.');
        }

        $validated = $request->validate([
            'tanggal' => 'required|array',
            'tanggal.*' => 'nullable|date',
        ]);

        $tanggalTerisi = collect($validated['tanggal'] ?? [])
            ->filter(fn($tgl) => !empty($tgl))
            ->map(fn($tgl) => (string) $tgl)
            ->values();

        $duplikatTanggal = $tanggalTerisi
            ->countBy()
            ->filter(fn($jumlah) => $jumlah > 1)
            ->keys()
            ->values();

        if ($duplikatTanggal->isNotEmpty()) {
            $tanggalList = $duplikatTanggal
                ->map(fn($tgl) => Carbon::parse($tgl)->format('d/m/Y'))
                ->implode(', ');

            return redirect()->back()
                ->withInput()
                ->with('error', 'Tanggal pertemuan tidak boleh sama dalam satu jadwal. Duplikat: ' . $tanggalList . '.');
        }

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

        return redirect('dosen/pertemuan/' . (int) $jadwal->id)
            ->with('status', 'Detail pertemuan berhasil disimpan.');
    }

    public function generateKode(Request $request, string $idJadwal)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $biodataId = $this->getBiodataId((int) $pegawai->id);

        $jadwal = DB::table('master_jadwal_temp')
            ->where('id', (int) $idJadwal)
            ->where('id_dosen', $biodataId)
            ->first();

        if (!$jadwal) {
            return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan atau bukan milik Anda.'], 404);
        }

        $validated = $request->validate([
            'id_pertemuan' => 'required|integer|min:1|max:16',
            'durasi' => 'required|in:5,10,15,30',
        ]);

        $pertemuan = MasterPertemuan::where('id_jadwal', (int) $idJadwal)
            ->where('id_pertemuan', (int) $validated['id_pertemuan'])
            ->first();

        if (!$pertemuan) {
            return response()->json(['success' => false, 'message' => 'Data pertemuan tidak ditemukan. Pastikan sudah set tanggal terlebih dahulu.'], 404);
        }

        $isRegenerate = !empty($pertemuan->kode_kelas);
        $isConfirmedRegenerate = (string) $request->input('confirm_regenerate', '0') === '1';

        if ($isRegenerate && !$isConfirmedRegenerate) {
            return response()->json([
                'success' => false,
                'message' => 'Generate ulang membutuhkan konfirmasi.',
            ], 422);
        }

        $kode = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));
        $expiredKode = Carbon::now()->addMinutes((int) $validated['durasi']);
        $pesertaCount = 0;

        DB::transaction(function () use ($pertemuan, $kode, $expiredKode, $idJadwal, $isRegenerate, &$pesertaCount) {
            $pertemuan->kode_kelas = $kode;
            $pertemuan->expired_kode = $expiredKode;
            $pertemuan->save();

            // Saat regenerate, hanya perbarui kode & waktu expired saja.
            // Status presensi mahasiswa tidak diubah.
            if (!$isRegenerate) {
                $nimList = DB::table('master_krs_temp')
                    ->where('id_jadwal', (int) $idJadwal)
                    ->pluck('nim');

                $pesertaCount = $nimList->count();

                foreach ($nimList as $nim) {
                    DB::table('master_presensi')->updateOrInsert(
                        [
                            'nim' => (string) $nim,
                            'id_jadwal' => (int) $idJadwal,
                            'tgl_pertemuan' => $pertemuan->tgl_pertemuan,
                        ],
                        [
                            'status' => 0,
                            'ttd' => null,
                            'log_date' => Carbon::now(),
                        ]
                    );
                }
            }
        });

        return response()->json([
            'success' => true,
            'kode' => $kode,
            'expired_kode' => $expiredKode->format('H:i:s'),
            'expired_full' => $expiredKode->format('d/m/Y H:i:s'),
            'is_regenerate' => $isRegenerate,
            'peserta_count' => $pesertaCount,
        ]);
    }

    public function uploadDokumen(Request $request, string $idJadwal)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $biodataId = $this->getBiodataId((int) $pegawai->id);

        $jadwal = DB::table('master_jadwal_temp')
            ->where('id', (int) $idJadwal)
            ->where('id_dosen', $biodataId)
            ->first();

        if (!$jadwal) {
            return redirect('dosen/pertemuan')->with('error', 'Data jadwal tidak ditemukan atau bukan milik Anda.');
        }

        $request->validate([
            'rps_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'kp_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if (!$request->hasFile('rps_file') && !$request->hasFile('kp_file')) {
            return redirect('dosen/pertemuan/' . (int) $jadwal->id)
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

        return redirect('dosen/pertemuan/' . (int) $jadwal->id)
            ->with('status', 'Dokumen RPS/KP berhasil diupload.');
    }

    public function exportPdf(string $idJadwal)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            return redirect()->route('pegawai.login');
        }

        $biodataId = $this->getBiodataId((int) $pegawai->id);

        $jadwal = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->leftJoin('program_studi as ps', 'ps.id', '=', 'mmk.id_program_studi')
            ->leftJoin('master_tahun_ajaran as mta', 'mta.id', '=', 'mjt.id_tahun')
            ->leftJoin('pegawai_biodata as pb', 'mjt.id_dosen', '=', 'pb.id')
            ->leftJoin('pegawai_biodata as pb2', 'mjt.id_dosen2', '=', 'pb2.id')
            ->select(
                'mjt.*',
                'mmk.nama_mata_kuliah',
                'mmk.id_program_studi',
                'mmk.jumlah_sks',
                'ps.jenjang as jenjang_prodi',
                'ps.nama_jurusan as nama_prodi',
                'mta.awal as tahun_awal',
                'mta.akhir as tahun_akhir',
                'mta.jenis as jenis_tahun',
                'pb.nip_pns as nip_dosen',
                'pb2.nip_pns as nip_dosen2',
                DB::raw("TRIM(CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,''))) as nama_dosen"),
                DB::raw("TRIM(CONCAT(COALESCE(pb2.gelar_depan,''), ' ', COALESCE(pb2.nama_lengkap,''), ' ', COALESCE(pb2.gelar_belakang,''))) as nama_dosen2")
            )
            ->where('mjt.id', (int) $idJadwal)
            ->where('mjt.id_dosen', $biodataId)
            ->first();

        if (!$jadwal) {
            return redirect('dosen/pertemuan')->with('error', 'Data jadwal tidak ditemukan atau bukan milik Anda.');
        }

        // Get all students in this class
        $mahasiswaList = DB::table('master_krs_temp as mkt')
            ->leftJoin('mahasiswa as m', 'mkt.nim', '=', 'm.nim')
            ->select(
                'mkt.nim',
                'm.nama',
                'mkt.id_jadwal'
            )
            ->where('mkt.id_jadwal', (int) $idJadwal)
            ->orderBy('m.nama')
            ->get();

        if ($mahasiswaList->isEmpty()) {
            return redirect('dosen/pertemuan/' . (int) $idJadwal)
                ->with('error', 'Tidak ada mahasiswa dalam kelas ini.');
        }

        // Get all pertemuan for this jadwal
        $pertemuanList = DB::table('master_pertemuan')
            ->select('id_pertemuan', 'tgl_pertemuan')
            ->where('id_jadwal', (int) $idJadwal)
            ->orderBy('id_pertemuan')
            ->get();

        // Fetch only needed presensi rows, then index by nim|tanggal for O(1) lookup in view.
        $nimList = $mahasiswaList->pluck('nim')->filter()->values()->all();
        $tanggalList = $pertemuanList
            ->pluck('tgl_pertemuan')
            ->filter(fn($tgl) => !empty($tgl))
            ->map(fn($tgl) => (string) $tgl)
            ->values()
            ->all();

        $presensiRows = DB::table('master_presensi')
            ->select('nim', 'tgl_pertemuan', 'status', 'ttd')
            ->where('id_jadwal', (int) $idJadwal)
            ->when(!empty($nimList), fn($q) => $q->whereIn('nim', $nimList))
            ->when(!empty($tanggalList), fn($q) => $q->whereIn('tgl_pertemuan', $tanggalList))
            ->get();

        $presensiIndex = [];
        $presensiCountByTanggal = [];
        foreach ($presensiRows as $row) {
            $tanggal = (string) $row->tgl_pertemuan;
            $key = (string) $row->nim . '|' . $tanggal;
            $status = $row->status !== null ? (int) $row->status : null;

            $presensiIndex[$key] = [
                'status' => $status,
                'ttd' => $row->ttd,
            ];

            if (!isset($presensiCountByTanggal[$tanggal])) {
                $presensiCountByTanggal[$tanggal] = [
                    'mhs_hadir' => 0,
                    'mhs_tidak_hadir' => 0,
                    'signatures' => [],
                ];
            }

            if ($status === 1) {
                $presensiCountByTanggal[$tanggal]['mhs_hadir']++;
                if (!empty($row->ttd)) {
                    $presensiCountByTanggal[$tanggal]['signatures'][] = $row->ttd;
                }
            } else {
                $presensiCountByTanggal[$tanggal]['mhs_tidak_hadir']++;
            }
        }

        $memoRows = DB::table('master_pertemuan as mp')
            ->leftJoin('tbl_memo as tm', 'tm.id_pertemuan', '=', 'mp.id')
            ->select(
                'mp.id',
                'mp.id_pertemuan',
                'mp.tgl_pertemuan',
                'tm.memo',
                'tm.sub',
                'tm.mhs_hadir',
                'tm.mhs_tidak_hadir'
            )
            ->where('mp.id_jadwal', (int) $idJadwal)
            ->orderBy('mp.id_pertemuan')
            ->get();

        $beritaAcaraRows = $memoRows->map(function ($row) use ($presensiCountByTanggal) {
            $tanggal = (string) ($row->tgl_pertemuan ?? '');
            $countRow = $presensiCountByTanggal[$tanggal] ?? [
                'mhs_hadir' => 0,
                'mhs_tidak_hadir' => 0,
                'signatures' => [],
            ];

            $randomSignature = null;
            if (!empty($countRow['signatures'])) {
                $randomSignature = $countRow['signatures'][array_rand($countRow['signatures'])];
            }

            return [
                'pertemuan_ke' => (int) ($row->id_pertemuan ?? 0),
                'rencana_tanggal' => $row->tgl_pertemuan,
                'tanggal_pelaksanaan' => $row->tgl_pertemuan,
                'materi' => trim((string) ($row->memo ?? '')),
                'sub_bahasan' => trim((string) ($row->sub ?? '')),
                'mhs_hadir' => (int) $countRow['mhs_hadir'],
                'mhs_tidak_hadir' => (int) $countRow['mhs_tidak_hadir'],
                'random_signature' => $randomSignature,
            ];
        })->values();

        $ketuaProdi = $this->getKetuaProdiByProgramStudi((string) ($jadwal->jenjang_prodi ?? ''), (int) ($jadwal->id_program_studi ?? 0));
        $ketuaSekolah = $this->getKetuaSekolahTinggi();

        $html = view('pegawai.absensi_pdf', [
            'jadwal' => $jadwal,
            'mahasiswaList' => $mahasiswaList,
            'pertemuanList' => $pertemuanList,
            'presensiIndex' => $presensiIndex,
            'beritaAcaraRows' => $beritaAcaraRows,
            'ketuaProdi' => $ketuaProdi,
            'ketuaSekolah' => $ketuaSekolah,
        ])->render();

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4-L', // Landscape
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        $mpdf->WriteHTML($html);
        $filename = 'absensi_dosen_' . ($jadwal->kode_mata_kuliah ?? 'jadwal') . '_' . now()->format('Ymd_His') . '.pdf';

        return response($mpdf->Output($filename, 'D'))
            ->header('Content-Type', 'application/pdf');
    }

    private function getKetuaProdiByProgramStudi(string $jenjangProdi, int $idProgramStudi): ?object
    {
        $jenjang = strtoupper(trim($jenjangProdi));
        if ($jenjang === '' && $idProgramStudi > 0) {
            $jenjangDb = DB::table('program_studi')
                ->where('id', $idProgramStudi)
                ->value('jenjang');
            $jenjang = strtoupper(trim((string) $jenjangDb));
        }

        $kolomNpp = str_contains($jenjang, 'D3') ? 'prodi_d3' : 'prodi_s1';

        $struktur = DB::table('struktur_pegawai2')->select($kolomNpp)->where('id', 1)->first();
        $nppKetua = trim((string) ($struktur->{$kolomNpp} ?? ''));

        if ($nppKetua === '') {
            return null;
        }

        return DB::table('pegawai as p')
            ->leftJoin('pegawai_biodata as pb', 'pb.id_pegawai', '=', 'p.id')
            ->select(
                'p.npp',
                'pb.nip_pns',
                DB::raw("TRIM(CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap, p.nama, ''), ' ', COALESCE(pb.gelar_belakang,''))) as nama_gelar")
            )
            ->where('p.npp', $nppKetua)
            ->first();
    }

    private function getKetuaSekolahTinggi(): ?object
    {
        $struktur = DB::table('struktur_pegawai2')->select('ketua_st')->where('id', 1)->first();
        $nppKetua = trim((string) ($struktur->ketua_st ?? ''));

        if ($nppKetua === '') {
            return null;
        }

        return DB::table('pegawai as p')
            ->leftJoin('pegawai_biodata as pb', 'pb.id_pegawai', '=', 'p.id')
            ->select(
                'p.npp',
                'pb.nip_pns',
                DB::raw("TRIM(CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap, p.nama, ''), ' ', COALESCE(pb.gelar_belakang,''))) as nama_gelar")
            )
            ->where('p.npp', $nppKetua)
            ->first();
    }
}
