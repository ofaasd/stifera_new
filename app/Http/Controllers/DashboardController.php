<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\PegawaiBiodatum;
use App\Models\MasterMataKuliah;
use App\Models\PmbPesertum;

class DashboardController extends Controller
{
    //
    public function index() {
        $CurrentPage = 'medical';
        $var = [];
        //hitung jumlah mahasiswa 
        $var['jumlah_mahasiswa'] = Mahasiswa::where('status',1)->count();
        $var['jumlah_dosen'] = PegawaiBiodatum::where('status_pegawai','aktif')->count();
        $var['matakuliah'] = MasterMataKuliah::where('is_aktif',1)->count();
        $var['mahasiswa_baru'] = PmbPesertum::where('angkatan',date('Y'))->count();
        return view('dashboard', compact('CurrentPage', 'var'));
	}
}
