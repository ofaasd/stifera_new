<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\PegawaiBiodatum;
use App\Models\MasterMataKuliah;
use App\Models\PmbPesertum;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function index() {
        $CurrentPage = 'dashboard';
        $var = [];
        //hitung jumlah mahasiswa 
        $var['jumlah_mahasiswa'] = Mahasiswa::where('status',1)->count();
        $var['jumlah_dosen'] = PegawaiBiodatum::where('status_pegawai','aktif')->count();
        $var['matakuliah'] = MasterMataKuliah::where('is_aktif',1)->count();
        $var['mahasiswa_baru'] = PmbPesertum::where('angkatan',date('Y'))->count();

        $var['pmb_per_tahun'] = PmbPesertum::query()
            ->selectRaw('angkatan as tahun, COUNT(*) as total')
            ->whereNotNull('angkatan')
            ->where('angkatan', '>', 0)
            ->groupBy('angkatan')
            ->orderBy('angkatan')
            ->get();

        $var['masukan_terbaru'] = DB::table('masukan as ms')
            ->leftJoin('mahasiswa as m', 'm.nim', '=', 'ms.nim')
            ->select('ms.nim', 'ms.saran', 'ms.tanggal', 'm.nama as nama_mahasiswa')
            ->orderByDesc('ms.tanggal')
            ->limit(10)
            ->get();

        return view('dashboard', compact('CurrentPage', 'var'));
	}
}
