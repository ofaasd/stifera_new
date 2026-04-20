<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MahasiswaMatakuliahController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $prodi = $this->getProdiMahasiswa((int) ($mahasiswa->id_program_studi ?? 0));
        $kodeMatakuliahDiambil = $this->getKodeMatakuliahDiambil((string) ($mahasiswa->nim ?? ''));
        $matakuliahProdi = $this->getMatakuliahProdi((int) ($mahasiswa->id_program_studi ?? 0), $kodeMatakuliahDiambil);

        $matakuliahDiambil = $matakuliahProdi->where('sudah_diambil', true)->values();
        $matakuliahBelumDiambil = $matakuliahProdi->where('sudah_diambil', false)->values();

        return view('mahasiswa.matakuliah', [
            'title' => 'Daftar Mata Kuliah',
            'CurrentPage' => 'content',
            'mahasiswa' => $mahasiswa,
            'prodi' => $prodi,
            'matakuliahDiambil' => $matakuliahDiambil,
            'matakuliahBelumDiambil' => $matakuliahBelumDiambil,
            'totalMatakuliah' => $matakuliahProdi->count(),
        ]);
    }

    private function getProdiMahasiswa(int $idProgramStudi): ?object
    {
        return DB::table('program_studi as ps')
            ->leftJoin('fakultas as f', 'f.id', '=', 'ps.fakultas')
            ->select(
                'ps.id',
                'ps.kode',
                'ps.jenjang',
                'ps.nama_jurusan',
                'f.nama_fakultas'
            )
            ->where('ps.id', $idProgramStudi)
            ->first();
    }

    private function getKodeMatakuliahDiambil(string $nim): array
    {
        $fromKrsTemp = DB::table('master_krs_temp')
            ->where('nim', $nim)
            ->pluck('mata_kuliah');

        $fromKrs = DB::table('master_krs')
            ->where('nim', $nim)
            ->pluck('mata_kuliah');

        $fromNilai = DB::table('master_nilai as mn')
            ->leftJoin('master_jadwal_temp as mjt', function ($join) {
                $join->on('mjt.id', '=', 'mn.id_jadwal')
                    ->on('mjt.id_tahun', '=', 'mn.id_tahun');
            })
            ->leftJoin('master_jadwal as mj', function ($join) {
                $join->on('mj.id_jadwal', '=', 'mn.id_jadwal')
                    ->on('mj.id_tahun', '=', 'mn.id_tahun');
            })
            ->where('mn.nim', $nim)
            ->selectRaw('COALESCE(mjt.kode_mata_kuliah, mj.kode_mata_kuliah) as kode_mata_kuliah')
            ->pluck('kode_mata_kuliah');

        return $fromKrsTemp
            ->merge($fromKrs)
            ->merge($fromNilai)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function getMatakuliahProdi(int $idProgramStudi, array $kodeMatakuliahDiambil): Collection
    {
        return DB::table('master_mata_kuliah as mmk')
            ->where('mmk.id_program_studi', $idProgramStudi)
            ->orderBy('mmk.semester')
            ->orderBy('mmk.kode_mata_kuliah')
            ->get()
            ->map(function ($row) use ($kodeMatakuliahDiambil) {
                $row->sudah_diambil = in_array($row->kode_mata_kuliah, $kodeMatakuliahDiambil, true);
                return $row;
            });
    }
}
