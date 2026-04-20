<?php

namespace App\Http\Controllers;

use App\Models\Masukan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MahasiswaMasukanController extends Controller
{
    public function create()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $masukanList = Masukan::query()
            ->where('nim', (string) $mahasiswa->nim)
            ->orderByDesc('tanggal')
            ->get();

        return view('mahasiswa.masukan.create', [
            'title' => 'Kritik & Saran',
            'CurrentPage' => 'content',
            'mahasiswa' => $mahasiswa,
            'masukanList' => $masukanList,
        ]);
    }

    public function store(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $validated = $request->validate([
            'saran' => ['required', 'string', 'max:5000'],
        ], [
            'saran.required' => 'Masukan wajib diisi.',
            'saran.max' => 'Masukan maksimal 5000 karakter.',
        ]);

        Masukan::create([
            'nim' => (string) $mahasiswa->nim,
            'saran' => $validated['saran'],
            'tanggal' => Carbon::now(),
            'status' => 'belum',
            'tindak_lanjut' => null,
            'tanggal_tanggapan' => null,
        ]);

        return redirect()->route('mahasiswa.masukan.create')->with('status', 'Masukan berhasil dikirim ke admin sistem.');
    }

    public function show(string $id)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $masukan = Masukan::query()
            ->where('id', $id)
            ->where('nim', (string) $mahasiswa->nim)
            ->first();

        if (!$masukan) {
            return redirect()->route('mahasiswa.masukan.create')->with('error', 'Data masukan tidak ditemukan.');
        }

        return view('mahasiswa.masukan.show', [
            'title' => 'Detail Masukan',
            'CurrentPage' => 'content',
            'mahasiswa' => $mahasiswa,
            'masukan' => $masukan,
        ]);
    }
}
