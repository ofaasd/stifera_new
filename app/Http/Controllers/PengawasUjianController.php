<?php

namespace App\Http\Controllers;

use App\Services\SyaratUjianService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mpdf\Mpdf;

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

        $this->ensureBeritaAcaraColumns();

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
                'mmk.semester as semester_mk',
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

        // Data berita acara yang tersimpan di DB
        $catatanPengawas = $pengaturan->{'catatan_pengawas_' . $jenis} ?? '';
        $namaPengawas = $pengaturan->{'nama_pengawas_' . $jenis} ?? '';
        $masterParaf = $pengaturan->{'master_paraf_' . $jenis} ?? '';

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
            'catatanPengawas' => $catatanPengawas,
            'namaPengawas' => $namaPengawas,
            'masterParaf' => $masterParaf,
        ]);
    }

    /**
     * Simpan paraf pengawas per mahasiswa via AJAX.
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

    /**
     * Simpan berita acara ujian & master paraf pengawas via AJAX.
     */
    public function simpanBeritaAcara(Request $request, string $jenis, string $token)
    {
        $jenis = strtolower($jenis);
        if (!in_array($jenis, ['uts', 'uas'])) {
            return response()->json(['success' => false, 'message' => 'Jenis ujian tidak valid.'], 400);
        }

        $tokenCol = $jenis === 'uts' ? 'token_pengawas_uts' : 'token_pengawas_uas';
        $pengaturan = DB::table('tbl_jadwal_ujian')->where($tokenCol, $token)->first();

        if (!$pengaturan) {
            return response()->json(['success' => false, 'message' => 'Token pengawas tidak valid atau kadaluarsa.'], 403);
        }

        $this->ensureBeritaAcaraColumns();

        $catatan = $request->input('catatan');
        $namaPengawas = $request->input('nama_pengawas');
        $masterParaf = $request->input('master_paraf');

        $updateData = [
            'catatan_pengawas_' . $jenis => $catatan,
            'nama_pengawas_' . $jenis => $namaPengawas,
            'log' => now(),
        ];

        if (!empty($masterParaf)) {
            $updateData['master_paraf_' . $jenis] = $masterParaf;
        }

        if (Schema::hasColumn('tbl_jadwal_ujian', 'catatan_pengawas_' . $jenis)) {
            DB::table('tbl_jadwal_ujian')
                ->where('id', $pengaturan->id)
                ->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berita acara ujian berhasil disimpan.',
            'catatan' => $catatan,
            'nama_pengawas' => $namaPengawas,
        ]);
    }

    /**
     * Export Berita Acara & Presensi Ujian ke PDF.
     */
    public function exportPdf(Request $request, string $jenis, string $token)
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

        $this->ensureBeritaAcaraColumns();

        // Ambil data catatan, nama pengawas, master paraf dari request atau dari database
        $catatan = trim((string) $request->input('catatan', $pengaturan->{'catatan_pengawas_' . $jenis} ?? ''));
        $namaPengawas = trim((string) $request->input('nama_pengawas', $pengaturan->{'nama_pengawas_' . $jenis} ?? ''));
        $masterParaf = trim((string) $request->input('master_paraf', $pengaturan->{'master_paraf_' . $jenis} ?? ''));

        // Simpan ke DB jika dikirim via POST
        if ($request->isMethod('post') && Schema::hasColumn('tbl_jadwal_ujian', 'catatan_pengawas_' . $jenis)) {
            $updateData = [
                'catatan_pengawas_' . $jenis => $catatan,
                'nama_pengawas_' . $jenis => $namaPengawas,
                'log' => now(),
            ];
            if (!empty($masterParaf)) {
                $updateData['master_paraf_' . $jenis] = $masterParaf;
            }
            DB::table('tbl_jadwal_ujian')->where('id', $pengaturan->id)->update($updateData);
        }

        $jadwal = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mjt.id_dosen')
            ->leftJoin('master_tahun_ajaran as mta', 'mta.id', '=', 'mjt.id_tahun')
            ->select(
                'mjt.*',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                'mmk.semester as semester_mk',
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

        // Jam & Tanggal Ujian
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

        $nims = $peserta->pluck('nim')->all();
        $batchKelayakan = SyaratUjianService::cekKelayakanBatch($nims, (int) $jadwal->id_tahun, $jenis);

        $totalPeserta = $peserta->count();
        $kursiHadir = [];
        $kursiTidakHadir = [];

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

            $noK = $p->no_kursi ?: $p->nim;
            if (!empty($p->paraf)) {
                $kursiHadir[] = $noK;
            } else {
                $kursiTidakHadir[] = $noK;
            }
        }

        $totalHadir = count($kursiHadir);
        $totalTidakHadir = count($kursiTidakHadir);
        $nomorKursiHadir = !empty($kursiHadir) ? implode(', ', $kursiHadir) : '-';
        $nomorKursiTidakHadir = !empty($kursiTidakHadir) ? implode(', ', $kursiTidakHadir) : '-';

        // Waktu Ujian – gunakan locale ID agar nama hari & bulan dalam Bahasa Indonesia
        $hariIndonesia = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $bulanIndonesia = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
        $tglAcuan = $tglTeori ?: ($tglPraktik ?: now()->toDateString());
        $cTgl = Carbon::parse($tglAcuan);
        $hariUjian  = $hariIndonesia[$cTgl->dayOfWeek];
        $tglUjian   = $cTgl->format('d');
        $bulanUjian = $bulanIndonesia[(int) $cTgl->format('n')];
        $tahunUjian = $cTgl->format('Y');

        $jamMulai = '-';
        $jamSelesai = '-';
        if (!empty($jamTeori)) {
            $jamMulai = Carbon::parse($jamTeori->mulai)->format('H:i');
            $jamSelesai = Carbon::parse($jamTeori->selesai)->format('H:i');
        } elseif (!empty($jamPraktik)) {
            $jamMulai = Carbon::parse($jamPraktik->mulai)->format('H:i');
            $jamSelesai = Carbon::parse($jamPraktik->selesai)->format('H:i');
        }

        $labelJenisUjian = $jenis === 'uts' ? 'Ujian Tengah Semester' : 'Ujian Akhir Semester';
        $labelTaJenis = ((int) ($jadwal->ta_jenis ?? 0) === 1) ? 'Gasal' : (((int) ($jadwal->ta_jenis ?? 0) === 2) ? 'Genap' : 'Antara');
        $taTeks = ($jadwal->ta_awal ?? '-') . ' / ' . ($jadwal->ta_akhir ?? '-');
        $semesterTeks = !empty($jadwal->semester_mk) ? (string) $jadwal->semester_mk : (!empty($jadwal->semester) ? (string) $jadwal->semester : '-');

        // Ketua Sekolah Tinggi
        $ketuaSekolah = $this->getKetuaSekolahTinggi();
        $namaKetuaSekolah = !empty($ketuaSekolah->nama_gelar) ? $ketuaSekolah->nama_gelar : 'apt.Rizki Ardian Hartanto, M.Farm.';
        $nipKetuaSekolah = !empty($ketuaSekolah->nip_pns) ? $ketuaSekolah->nip_pns : (!empty($ketuaSekolah->npp) ? $ketuaSekolah->npp : '071117057');

        $tglCetak = Carbon::now()->format('d') . ' ' . $bulanIndonesia[(int) Carbon::now()->format('n')] . ' ' . Carbon::now()->format('Y');

        $pdfData = [
            'jenis' => $jenis,
            'jadwal' => $jadwal,
            'peserta' => $peserta,
            'totalPeserta' => $totalPeserta,
            'totalHadir' => $totalHadir,
            'totalTidakHadir' => $totalTidakHadir,
            'nomorKursiHadir' => $nomorKursiHadir,
            'nomorKursiTidakHadir' => $nomorKursiTidakHadir,
            'hariUjian' => $hariUjian,
            'tglUjian' => $tglUjian,
            'bulanUjian' => $bulanUjian,
            'tahunUjian' => $tahunUjian,
            'jamMulai' => $jamMulai,
            'jamSelesai' => $jamSelesai,
            'labelJenisUjian' => $labelJenisUjian,
            'labelTaJenis' => $labelTaJenis,
            'taTeks' => $taTeks,
            'semesterTeks' => $semesterTeks,
            'catatan' => $catatan,
            'namaPengawas' => $namaPengawas,
            'masterParaf' => $masterParaf,
            'namaKetuaSekolah' => $namaKetuaSekolah,
            'nipKetuaSekolah' => $nipKetuaSekolah,
            'tglCetak' => $tglCetak,
        ];

        $html = view('pengawas.berita_acara_pdf', $pdfData)->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'default_font' => 'times',
        ]);

        $mpdf->WriteHTML($html);

        $filename = 'Berita_Acara_Ujian_' . strtoupper($jenis) . '_' . ($jadwal->kode_mata_kuliah ?? 'MK') . '_' . now()->format('Ymd_His') . '.pdf';

        return response($mpdf->Output($filename, 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    /**
     * Ambil data pejabat Ketua Sekolah Tinggi dari struktur_pegawai2.
     */
    private function getKetuaSekolahTinggi(): ?object
    {
        try {
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
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Memastikan kolom-kolom berita acara pengawas tersedia pada tbl_jadwal_ujian.
     */
    private function ensureBeritaAcaraColumns(): void
    {
        try {
            if (Schema::hasTable('tbl_jadwal_ujian') && !Schema::hasColumn('tbl_jadwal_ujian', 'catatan_pengawas_uts')) {
                Schema::table('tbl_jadwal_ujian', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->text('catatan_pengawas_uts')->nullable();
                    $table->text('catatan_pengawas_uas')->nullable();
                    $table->string('nama_pengawas_uts', 150)->nullable();
                    $table->string('nama_pengawas_uas', 150)->nullable();
                    $table->longText('master_paraf_uts')->nullable();
                    $table->longText('master_paraf_uas')->nullable();
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika tidak memiliki izin DDL langsung
        }
    }
}
