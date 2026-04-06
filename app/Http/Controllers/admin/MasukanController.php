<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Masukan;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class MasukanController extends Controller
{
    public function index()
    {
        $data['title'] = 'Daftar Masukan';
        $data['CurrentPage'] = 'content';
        $data['masukanList'] = Masukan::orderByDesc('tanggal')->get();

        return view('admin.masukan.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Masukan';
        $data['CurrentPage'] = 'content';
        $data['mahasiswaList'] = Mahasiswa::orderBy('nim')->get(['nim', 'nama']);

        return view('admin.masukan.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim'               => 'required|string|max:20',
            'saran'             => 'required|string',
            'tanggal'           => 'required|date',
            'status'            => 'required|in:belum,proses,selesai',
            'tindak_lanjut'     => 'nullable|string',
            'tanggal_tanggapan' => 'nullable|date',
        ]);

        Masukan::create($validated);

        return redirect()->route('masukan.index')->with('status', 'Masukan berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $masukan = Masukan::find($id);
        if (!$masukan) {
            return redirect()->route('masukan.index')->with('error', 'Data masukan tidak ditemukan.');
        }

        $data['title'] = 'Edit Masukan';
        $data['CurrentPage'] = 'content';
        $data['d'] = $masukan;
        $data['mahasiswaList'] = Mahasiswa::orderBy('nim')->get(['nim', 'nama']);

        return view('admin.masukan.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $masukan = Masukan::find($id);
        if (!$masukan) {
            return redirect()->route('masukan.index')->with('error', 'Data masukan tidak ditemukan.');
        }

        $validated = $request->validate([
            'nim'               => 'required|string|max:20',
            'saran'             => 'required|string',
            'tanggal'           => 'required|date',
            'status'            => 'required|in:belum,proses,selesai',
            'tindak_lanjut'     => 'nullable|string',
            'tanggal_tanggapan' => 'nullable|date',
        ]);

        $masukan->update($validated);

        return redirect()->route('masukan.index')->with('status', 'Masukan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $masukan = Masukan::find($id);
        if (!$masukan) {
            return redirect()->route('masukan.index')->with('error', 'Data masukan tidak ditemukan.');
        }

        $masukan->delete();

        return redirect()->route('masukan.index')->with('status', 'Masukan berhasil dihapus.');
    }
}
