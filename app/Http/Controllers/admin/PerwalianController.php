<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\PegawaiBiodatum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerwalianController extends Controller
{
    public function index()
    {
        $data['title'] = 'Manajemen Perwalian';
        $data['CurrentPage'] = 'content';

        $data['mahasiswaList'] = DB::table('mahasiswa as m')
            ->leftJoin('program_studi as ps', 'm.id_program_studi', '=', 'ps.id')
            ->leftJoin('pegawai_biodata as pb', 'm.id_dsn_wali', '=', 'pb.id_pegawai')
            ->select(
                'm.id',
                'm.nim',
                'm.nama',
                'm.id_dsn_wali',
                'ps.nama_jurusan as nama_jurusan',
                'pb.nama_lengkap as dosen_wali_nama',
                'pb.nidn as dosen_wali_nidn'
            )
            ->where('m.status', 1)
            ->orderBy('m.nim')
            ->get();

        return view('admin.perwalian.index', $data);
    }

    public function cariDosen(Request $request)
    {
        $keyword = trim((string) $request->input('q', ''));

        $query = PegawaiBiodatum::query()
            ->select('id_pegawai', 'nama_lengkap', 'gelar_depan', 'gelar_belakang', 'nidn', 'status_pegawai')
            ->whereNotNull('id_pegawai')
            ->whereNotNull('nama_lengkap');

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_lengkap', 'like', '%' . $keyword . '%')
                    ->orWhere('nidn', 'like', '%' . $keyword . '%');
            });
        }

        $dosenList = $query
            ->orderByRaw("CASE WHEN LOWER(COALESCE(status_pegawai, '')) = 'aktif' THEN 0 ELSE 1 END")
            ->limit(30)
            ->get();

        $results = $dosenList->map(function ($dosen) {
            $text = trim(($dosen->gelar_depan ?? '') . ' ' . ($dosen->nama_lengkap ?? '') . ' ' . ($dosen->gelar_belakang ?? ''));
            $text = preg_replace('/\s+/', ' ', $text);
            if (empty($text)) {
                $text = trim((string) $dosen->nama_lengkap);
                $text = preg_replace('/\s+/', ' ', $text);
            }

            if (!empty($dosen->nidn)) {
                $text .= ' (' . $dosen->nidn . ')';
            }

            return [
                'id' => (string) $dosen->id_pegawai,
                'text' => $text,
            ];
        })->sortBy('text', SORT_NATURAL | SORT_FLAG_CASE)->values();

        return response()->json([
            'results' => $results,
        ]);
    }

    public function updateDosenWali(Request $request)
    {
        $request->merge([
            'id_mahasiswa' => $request->input('id_mahasiswa', $request->input('id')),
        ]);

        $request->validate([
            'id_mahasiswa' => 'required|integer|exists:mahasiswa,id',
            'id_dsn_wali' => 'nullable|integer|exists:pegawai_biodata,id_pegawai',
        ]);

        $mahasiswa = Mahasiswa::findOrFail($request->id_mahasiswa);
        $mahasiswa->update([
            'id_dsn_wali' => $request->id_dsn_wali ?? 0,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dosen wali berhasil diperbarui',
                'data' => $mahasiswa,
            ]);
        }

        return redirect()->back()->with('status', 'Dosen wali berhasil diperbarui');
    }
}
