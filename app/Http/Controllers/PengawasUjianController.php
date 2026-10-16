<?php

namespace App\Http\Controllers;

use App\Services\SyaratUjianService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengawasUjianController extends Controller
{
    /**
     * Halaman Publik Pengawas Ujian (Tanpa Login).
     */
    public function index(string $jenis, string $token)
    {
        $jenis = strtolower($jenis);
        if (!in_array($jenis, ['uts', 'uas'])) {
            abort(404, 'Jenis ujian tidak valid.');
        }

        $tokenCol = $jenis === 'uts' ? 'token_pengawas_uts' : 'token_pengawas_uas';
        $pengaturan = DB::table('tbl_jadwal_ujian')->where($tokenCol, $token)->first();

        if (!$pengaturan) {
            return response()->view('pengawas.not_found', [
                'message' => 'Tautan pengawas ujian tidak valid atau telah kadaluarsa.',
            ], 404);
        }

        $jadwal = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mjt.id_dosen')
            ->leftJoin('master_tahun_ajaran as mta', 'mta.id', '=', 'mjt.id_tahun')
            ->select(
                'mjt.*',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                'mta.awal as ta_awal',
                'mta.akhir as ta_akhir',
                'mta.jenis as ta_jenis',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->where('mjt.id', $pengaturan->id_jadwal)
            ->first();

        if (!$jadwal) {
            abort(404, 'Data jadwal mata kuliah tidak ditemukan.');
        }

        // Ambil info jam dan tanggal ujian
        if ($jenis === 'uts') {
            $tglTeori = $pengaturan->tanggal_uts_t;
            $jamTeori = DB::table('master_jam')->where('id', $pengaturan->id_jam_uts_t)->first();
            $tglPraktik = $pengaturan->tanggal_uts_p;
            $jamPraktik = DB::table('master_jam')->where('id', $pengaturan->id_jam_uts_p)->first();
        } else {
            $tglTeori = $pengaturan->tanggal_uas_t;
            $jamTeori = DB::table('master_jam')->where('id', $pengaturan->id_jam_uas_t)->first();
            $tglPraktik = $pengaturan->tanggal_uas_p;
            $jamPraktik = DB::table('master_jam')->where('id', $pengaturan->id_jam_uas_p)->first();
        }

        // Sinkronisasi peserta ujian: pastikan mahasiswa di master_krs_temp masuk ke tbl_tempat_ujian
        $enrolledNims = DB::table('master_krs_temp')
            ->where('id_jadwal', $jadwal->id)
            ->where('id_tahun', (int) $jadwal->id_tahun)
            ->orderBy('nim')
            ->pluck('nim')
            ->values();

        $existingNims = DB::table('tbl_tempat_ujian')
            ->where('id_jadwal', $jadwal->id)
            ->where('ta', (int) $jadwal->id_tahun)
            ->pluck('nim')
            ->all();

        $missingNims = $enrolledNims->diff($existingNims);
        if ($missingNims->isNotEmpty()) {
            $maxKursi = (int) DB::table('tbl_tempat_ujian')
                ->where('id_jadwal', $jadwal->id)
                ->where('ta', (int) $jadwal->id_tahun)
                ->max('no_kursi');

            foreach ($missingNims as $mNim) {
                $maxKursi++;
                DB::table('tbl_tempat_ujian')->insert([
                    'id_jadwal' => $jadwal->id,
                    'ta' => (int) $jadwal->id_tahun,
                    'nim' => (string) $mNim,
                    'no_kursi' => $maxKursi,
                    'ruang' => $jadwal->ruang ?? '-',
                    'log' => now(),
                ]);
            }
        }

        // Ambil daftar peserta dari tbl_tempat_ujian
        $peserta = DB::table('tbl_tempat_ujian as ttu')
            ->leftJoin('mahasiswa as m', 'm.nim', '=', 'ttu.nim')
            ->select(
                'ttu.id',
                'ttu.nim',
                'm.nama as nama_mahasiswa',
                'ttu.no_kursi',
                'ttu.ruang',
                'ttu.paraf_pengawas_uts',
                'ttu.waktu_paraf_uts',
                'ttu.paraf_pengawas_uas',
                'ttu.waktu_paraf_uas'
            )
            ->where('ttu.id_jadwal', $jadwal->id)
            ->where('ttu.ta', (int) $jadwal->id_tahun)
            ->orderByRaw('CAST(ttu.no_kursi AS UNSIGNED) ASC')
            ->orderBy('ttu.nim', 'ASC')
            ->get();

        // Validasi kelayakan ujian terpusat
        $nims = $peserta->pluck('nim')->all();
        $batchKelayakan = SyaratUjianService::cekKelayakanBatch($nims, (int) $jadwal->id_tahun, $jenis);

        $totalPeserta = $peserta->count();
        $totalLolos = 0;
        $totalDiparaf = 0;

        foreach ($peserta as $p) {
            $info = $batchKelayakan[$p->nim] ?? [
                'is_lolos' => false,
                'syarat_dosen_wali' => false,
                'syarat_kepala_bak' => false,
                'keterangan' => 'Belum Validasi Dosen Wali & Kepala BAK',
            ];

            $p->is_lolos = $info['is_lolos'];
            $p->syarat_dosen_wali = $info['syarat_dosen_wali'];
            $p->syarat_kepala_bak = $info['syarat_kepala_bak'];
            $p->keterangan = $info['keterangan'];

            $p->paraf = $jenis === 'uts' ? $p->paraf_pengawas_uts : $p->paraf_pengawas_uas;
            $p->waktu_paraf = $jenis === 'uts' ? $p->waktu_paraf_uts : $p->waktu_paraf_uas;

            if ($p->is_lolos) {
                $totalLolos++;
            }
            if (!empty($p->paraf)) {
                $totalDiparaf++;
            }
        }

        return view('pengawas.ujian', [
            'jenis' => $jenis,
            'token' => $token,
            'jadwal' => $jadwal,
            'pengaturan' => $pengaturan,
            'tglTeori' => $tglTeori,
            'jamTeori' => $jamTeori,
            'tglPraktik' => $tglPraktik,
            'jamPraktik' => $jamPraktik,
            'peserta' => $peserta,
            'totalPeserta' => $totalPeserta,
            'totalLolos' => $totalLolos,
            'totalDiparaf' => $totalDiparaf,
        ]);
    }

    /**
     * Simpan paraf pengawas via AJAX.
     */
    public function simpanParaf(Request $request, string $jenis, string $token)
    {
        $request->validate([
            'nim' => ['required', 'string'],
            'paraf' => ['required', 'string'],
        ]);

        $jenis = strtolower($jenis);
        if (!in_array($jenis, ['uts', 'uas'])) {
            return response()->json(['success' => false, 'message' => 'Jenis ujian tidak valid.'], 400);
        }

        $tokenCol = $jenis === 'uts' ? 'token_pengawas_uts' : 'token_pengawas_uas';
        $pengaturan = DB::table('tbl_jadwal_ujian')->where($tokenCol, $token)->first();

        if (!$pengaturan) {
            return response()->json(['success' => false, 'message' => 'Token pengawas tidak valid atau kadaluarsa.'], 403);
        }

        $paraf = $request->input('paraf');
        if (!preg_match('/^data:image\/png;base64,[A-Za-z0-9+\/=]+$/', $paraf)) {
            return response()->json(['success' => false, 'message' => 'Format paraf harus berupa Base64 PNG yang valid.'], 422);
        }

        $nim = $request->input('nim');

        // Cek kelayakan mahasiswa
        $kelayakan = SyaratUjianService::cekKelayakan($nim, (int) $pengaturan->ta, $jenis);
        if (!$kelayakan['is_lolos']) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa ini tidak berhak mengikuti ujian: ' . $kelayakan['keterangan'],
            ], 422);
        }

        $now = Carbon::now();
        $updateData = $jenis === 'uts'
            ? [
                'paraf_pengawas_uts' => $paraf,
                'waktu_paraf_uts' => $now,
                'log' => $now,
            ]
            : [
                'paraf_pengawas_uas' => $paraf,
                'waktu_paraf_uas' => $now,
                'log' => $now,
            ];

        $updated = DB::table('tbl_tempat_ujian')
            ->where('id_jadwal', $pengaturan->id_jadwal)
            ->where('ta', $pengaturan->ta)
            ->where('nim', $nim)
            ->update($updateData);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan pada jadwal ini.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Paraf pengawas untuk mahasiswa ' . $nim . ' berhasil disimpan.',
            'paraf' => $paraf,
            'waktu_paraf' => $now->translatedFormat('d-m-Y H:i'),
            'nim' => $nim,
        ]);
    }
}
