<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PegawaiPerwalianController extends Controller
{
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $dosenInfo = DB::table('pegawai_biodata')
            ->select('nama_lengkap', 'nidn')
            ->where('id_pegawai', (int) $pegawai->id)
            ->first();

        $mahasiswaList = DB::table('mahasiswa as m')
            ->leftJoin('program_studi as ps', 'm.id_program_studi', '=', 'ps.id')
            ->leftJoin('pegawai_biodata as pb', 'm.id_dsn_wali', '=', 'pb.id_pegawai')
            ->leftJoin(
                DB::raw('(SELECT nim, MAX(is_publish) as is_publish FROM master_krs_temp GROUP BY nim) as krs'),
                'krs.nim',
                '=',
                'm.nim'
            )
            ->select(
                'm.id',
                'm.nim',
                'm.nama',
                'm.id_dsn_wali',
                'ps.nama_jurusan as nama_jurusan',
                'pb.nama_lengkap as dosen_wali_nama',
                'pb.nidn as dosen_wali_nidn',
                'krs.is_publish as status_krs'
            )
            ->where('m.status', 1)
            ->where('m.id_dsn_wali', (int) $pegawai->id)
            ->orderBy('m.nim')
            ->get();

        return view('pegawai.perwalian.index', [
            'title' => 'Perwalian Dosen',
            'CurrentPage' => 'content',
            'mahasiswaList' => $mahasiswaList,
            'pegawai' => $pegawai,
            'dosenInfo' => $dosenInfo,
        ]);
    }

    public function verifikasiKrs(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $request->validate([
            'nim'        => 'required|string|max:20',
            'status_krs' => 'required|in:0,1',
        ]);

        $nim       = $request->input('nim');
        $statusKrs = (int) $request->input('status_krs');

        // Pastikan mahasiswa tersebut memang wali dari dosen yang login
        $isWali = DB::table('mahasiswa')
            ->where('nim', $nim)
            ->where('id_dsn_wali', (int) $pegawai->id)
            ->exists();

        if (!$isWali) {
            return response()->json(['result' => 0, 'message' => 'Akses ditolak.'], 403);
        }

        $updated = DB::table('master_krs_temp')
            ->where('nim', $nim)
            ->update(['is_publish' => $statusKrs]);

        if ($updated !== false) {
            return response()->json(['result' => 1, 'message' => 'Status KRS berhasil diperbarui.']);
        }

        return response()->json(['result' => 0, 'message' => 'Tidak ada KRS yang diperbarui.']);
    }

    public function getKrs(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $request->validate([
            'nim' => 'required|string|max:20',
        ]);

        $nim = $request->input('nim');

        // Pastikan mahasiswa tersebut memang wali dari dosen yang login
        $isWali = DB::table('mahasiswa')
            ->where('nim', $nim)
            ->where('id_dsn_wali', (int) $pegawai->id)
            ->exists();

        if (!$isWali) {
            return response()->json(['result' => 0, 'message' => 'Akses ditolak.'], 403);
        }

        $mahasiswa = DB::table('mahasiswa')
            ->select('tipe_mhs')
            ->where('nim', $nim)
            ->first();

        $tipeMhs = (int) ($mahasiswa->tipe_mhs ?? 0);
        $tahunAktif = DB::table('master_tahun_ajaran')
            ->where('is_aktif', 1)
            ->where('tipe_mhs', $tipeMhs)
            ->orderByDesc('id')
            ->first();

        if (!$tahunAktif) {
            $tahunAktif = DB::table('master_tahun_ajaran')
                ->where('tipe_mhs', $tipeMhs)
                ->orderByDesc('id')
                ->first();
        }

        $idTahunKrs = (int) ($tahunAktif->id ?? 0);
        if ($idTahunKrs <= 0) {
            $idTahunKrs = (int) DB::table('master_krs_temp')
                ->where('nim', $nim)
                ->orderByDesc('id_tahun')
                ->value('id_tahun');
        }

        if ($idTahunKrs <= 0) {
            return response()->json([
                'result' => 1,
                'data' => [],
            ]);
        }

        $krsData = DB::table('master_krs_temp as mkt')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mkt.mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mkt.id_dosen')
            ->where('mkt.nim', $nim)
            ->where('mkt.id_tahun', $idTahunKrs)
            ->select(
                'mkt.id',
                'mkt.mata_kuliah',
                'mkt.sks',
                'mkt.hari',
                'mkt.sesi',
                'mkt.ruang',
                'mkt.is_publish',
                'mmk.nama_mata_kuliah',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->orderBy('mkt.id')
            ->get();

        return response()->json([
            'result' => $krsData->isNotEmpty() ? 1 : 1,
            'data' => $krsData,
        ]);
    }
}
