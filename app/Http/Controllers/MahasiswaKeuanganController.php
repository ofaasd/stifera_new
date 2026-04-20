<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class MahasiswaKeuanganController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        return view('mahasiswa.keuangan', [
            'title' => 'Keuangan Mahasiswa',
            'CurrentPage' => 'content',
            'mahasiswa' => $mahasiswa,
        ]);
    }
}
