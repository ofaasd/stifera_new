<?php

namespace App\Http\Controllers;

use App\Models\MasterPertemuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $pertemuanByNomor   = [];
        foreach ($existing as $row) {
            $tanggalByPertemuan[(int) $row->id_pertemuan] = optional($row->tgl_pertemuan)->format('Y-m-d');
            $pertemuanByNomor[(int) $row->id_pertemuan]   = $row;
        }

        return view('pegawai.pertemuan.detail', [
            'title' => 'Detail Setting Pertemuan',
            'CurrentPage' => 'content',
            'jadwal' => $jadwal,
            'tanggalByPertemuan' => $tanggalByPertemuan,
            'pertemuanByNomor'   => $pertemuanByNomor,
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
            'tanggal'   => 'required|array',
            'tanggal.*' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated, $jadwal) {
            for ($i = 1; $i <= 16; $i++) {
                $tanggal = $validated['tanggal'][$i] ?? null;

                if (!empty($tanggal)) {
                    MasterPertemuan::updateOrCreate(
                        [
                            'id_jadwal'    => (int) $jadwal->id,
                            'id_tahun'     => (int) $jadwal->id_tahun,
                            'id_pertemuan' => $i,
                        ],
                        [
                            'tgl_pertemuan'   => $tanggal,
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
            'durasi'       => 'required|in:5,10,15',
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
                'message' => 'Generate ulang membutuhkan konfirmasi karena semua mahasiswa akan di-set tidak hadir (status 0).',
            ], 422);
        }

        $kode        = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));
        $expiredKode = Carbon::now()->addMinutes((int) $validated['durasi']);
        $pesertaCount = 0;

        DB::transaction(function () use ($pertemuan, $kode, $expiredKode, $idJadwal, &$pesertaCount) {
            $pertemuan->kode_kelas   = $kode;
            $pertemuan->expired_kode = $expiredKode;
            $pertemuan->save();

            $nimList = DB::table('master_krs_temp')
                ->where('id_jadwal', (int) $idJadwal)
                ->pluck('nim');

            $pesertaCount = $nimList->count();

            foreach ($nimList as $nim) {
                DB::table('master_presensi')->updateOrInsert(
                    [
                        'nim'           => (string) $nim,
                        'id_jadwal'     => (int) $idJadwal,
                        'tgl_pertemuan' => $pertemuan->tgl_pertemuan,
                    ],
                    [
                        'status'   => 0,
                        'ttd'      => null,
                        'log_date' => Carbon::now(),
                    ]
                );
            }
        });

        return response()->json([
            'success'      => true,
            'kode'         => $kode,
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
            'kp_file'  => 'nullable|file|mimes:pdf,doc,docx|max:5120',
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
}
