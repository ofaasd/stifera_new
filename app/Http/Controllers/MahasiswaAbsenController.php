<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MahasiswaAbsenController extends Controller
{
    // ─── Step 1: Show enter-code form ────────────────────────────────────────
    public function index(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $prefillKode = strtoupper((string) $request->query('kode', ''));
        $prefillKode = preg_replace('/[^A-Z0-9]/', '', $prefillKode);
        $prefillKode = substr((string) $prefillKode, 0, 6);

        return view('mahasiswa.absen_kode', [
            'title'       => 'Absen Online',
            'CurrentPage' => 'content',
            'mahasiswa'   => $mahasiswa,
            'prefillKode' => $prefillKode,
        ]);
    }

    // ─── Step 2: Verify code, store pertemuan id in session, go to sign page ─
    public function verifikasi(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $request->validate([
            'kode_kelas' => 'required|string|size:6',
        ]);

        $kode = strtoupper(trim($request->input('kode_kelas')));
        $nim  = (string) ($mahasiswa->nim ?? '');
        $now  = Carbon::now();
        $today = $now->toDateString();

        // Find a pertemuan matching the code that:
        // - tgl_pertemuan = today
        // - kunci_kehadiran = 0
        // - expired_kode > now
        $pertemuan = DB::table('master_pertemuan')
            ->whereDate('tgl_pertemuan', $today)
            ->where('kunci_kehadiran', 0)
            ->where('kode_kelas', $kode)
            ->where('expired_kode', '>', $now)
            ->first();

        if (!$pertemuan) {
            return back()
                ->withInput()
                ->with('error', 'Kode tidak ditemukan, sudah expired, atau absen sudah dikunci. Pastikan kode yang dimasukkan benar.');
        }

        // Check student is enrolled in this jadwal
        $enrolled = DB::table('master_krs_temp')
            ->where('nim', $nim)
            ->where('id_jadwal', $pertemuan->id_jadwal)
            ->exists();

        if (!$enrolled) {
            return back()
                ->withInput()
                ->with('error', 'Anda tidak terdaftar pada mata kuliah ini.');
        }

        // Check not already marked Hadir for today's pertemuan
        $existing = DB::table('master_presensi')
            ->where('nim', $nim)
            ->where('id_jadwal', $pertemuan->id_jadwal)
            ->whereDate('tgl_pertemuan', $today)
            ->first();

        if ($existing && (int) $existing->status === 1) {
            return back()
                ->withInput()
                ->with('error', 'Anda sudah melakukan absen Hadir untuk pertemuan ini hari ini.');
        }

        // Store verified pertemuan id in session (CSRF-safe token)
        $token = bin2hex(random_bytes(16));
        session([
            'absen_token'       => $token,
            'absen_id_pertemuan' => $pertemuan->id,
            'absen_nim'         => $nim,
        ]);

        return redirect()->route('mahasiswa.absen.ttd');
    }

    // ─── Step 3: Show signature pad page ─────────────────────────────────────
    public function ttd()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $idPertemuan = session('absen_id_pertemuan');
        $token       = session('absen_token');
        $nim         = session('absen_nim');

        if (!$idPertemuan || !$token || $nim !== (string) ($mahasiswa->nim ?? '')) {
            return redirect()->route('mahasiswa.absen.index')
                ->with('error', 'Sesi absen tidak valid. Silakan masukkan kode ulang.');
        }

        $pertemuan = DB::table('master_pertemuan as mp')
            ->leftJoin('master_jadwal_temp as mjt', 'mp.id_jadwal', '=', 'mjt.id')
            ->leftJoin('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'mjt.id_dosen', '=', 'pb.id')
            ->select(
                'mp.*',
                'mjt.kode_mata_kuliah',
                'mjt.hari',
                'mjt.sesi',
                'mjt.ruang',
                'mmk.nama_mata_kuliah',
                DB::raw("TRIM(CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,''))) as nama_dosen")
            )
            ->where('mp.id', $idPertemuan)
            ->first();

        if (!$pertemuan) {
            return redirect()->route('mahasiswa.absen.index')
                ->with('error', 'Data pertemuan tidak ditemukan.');
        }

        // Re-check the code hasn't expired while student was on the code form
        if (Carbon::parse($pertemuan->expired_kode)->isPast()) {
            session()->forget(['absen_token', 'absen_id_pertemuan', 'absen_nim']);
            return redirect()->route('mahasiswa.absen.index')
                ->with('error', 'Kode absen telah expired. Minta dosen untuk membuat kode baru.');
        }

        return view('mahasiswa.absen_ttd', [
            'title'       => 'Tanda Tangan Absen',
            'CurrentPage' => 'content',
            'mahasiswa'   => $mahasiswa,
            'pertemuan'   => $pertemuan,
            'token'       => $token,
        ]);
    }

    // ─── Step 4: Save presensi + signature ───────────────────────────────────
    public function simpan(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $idPertemuan = session('absen_id_pertemuan');
        $token       = session('absen_token');
        $nim         = session('absen_nim');

        if (!$idPertemuan || !$token || $nim !== (string) ($mahasiswa->nim ?? '')) {
            return redirect()->route('mahasiswa.absen.index')
                ->with('error', 'Sesi absen tidak valid.');
        }

        if ($request->input('absen_token') !== $token) {
            return redirect()->route('mahasiswa.absen.index')
                ->with('error', 'Token tidak cocok. Silakan ulangi proses absen.');
        }

        $request->validate([
            'ttd' => 'required|string',
        ]);

        $pertemuan = DB::table('master_pertemuan')
            ->where('id', $idPertemuan)
            ->where('kunci_kehadiran', 0)
            ->first();

        if (!$pertemuan) {
            session()->forget(['absen_token', 'absen_id_pertemuan', 'absen_nim']);
            return redirect()->route('mahasiswa.absen.index')
                ->with('error', 'Absen sudah dikunci oleh dosen.');
        }

        // Re-verify code not expired
        if (Carbon::parse($pertemuan->expired_kode)->isPast()) {
            session()->forget(['absen_token', 'absen_id_pertemuan', 'absen_nim']);
            return redirect()->route('mahasiswa.absen.index')
                ->with('error', 'Kode absen telah expired.');
        }

        $today = Carbon::now()->toDateString();

        // Check not already hadir
        $existing = DB::table('master_presensi')
            ->where('nim', $nim)
            ->where('id_jadwal', $pertemuan->id_jadwal)
            ->whereDate('tgl_pertemuan', $today)
            ->first();

        if ($existing && (int) $existing->status === 1) {
            session()->forget(['absen_token', 'absen_id_pertemuan', 'absen_nim']);
            return redirect()->route('mahasiswa.absen.index')
                ->with('error', 'Anda sudah tercatat Hadir untuk pertemuan ini.');
        }

        // Validate signature is a valid base64 PNG data URI
        $ttdRaw = $request->input('ttd');
        if (!preg_match('/^data:image\/png;base64,[A-Za-z0-9+\/=]+$/', $ttdRaw)) {
            return back()->with('error', 'Tanda tangan tidak valid. Silakan ulangi.');
        }

        DB::table('master_presensi')->updateOrInsert(
            [
                'nim'           => $nim,
                'id_jadwal'     => $pertemuan->id_jadwal,
                'tgl_pertemuan' => $pertemuan->tgl_pertemuan,
            ],
            [
                'status'   => 1,
                'ttd'      => $ttdRaw,
                'log_date' => Carbon::now(),
            ]
        );

        session()->forget(['absen_token', 'absen_id_pertemuan', 'absen_nim']);

        return redirect()->route('mahasiswa.presensi.index')
            ->with('status', 'Absen berhasil dicatat. Terima kasih!');
    }
}
