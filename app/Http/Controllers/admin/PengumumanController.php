<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterPost;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index()
    {
        $data['title'] = 'Pengumuman';
        $data['CurrentPage'] = 'content';
        $data['pengumumanList'] = MasterPost::where('kategori', 1)
            ->orderByDesc('id')
            ->get();

        return view('admin.pengumuman.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Pengumuman';
        $data['CurrentPage'] = 'content';

        return view('admin.pengumuman.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
            'gambar' => 'required|string|max:255',
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
        ]);

        MasterPost::create([
            'judul' => trim($validated['judul']),
            'isi' => $validated['isi'],
            'gambar' => trim($validated['gambar']),
            'tgl_awal' => $validated['tgl_awal'],
            'tgl_akhir' => $validated['tgl_akhir'],
            'kategori' => 1,
        ]);

        return redirect('pengumuman')->with('status', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $pengumuman = MasterPost::where('kategori', 1)->find($id);
        if (!$pengumuman) {
            return redirect('pengumuman')->with('error', 'Data pengumuman tidak ditemukan.');
        }

        $data['title'] = 'Edit Pengumuman';
        $data['CurrentPage'] = 'content';
        $data['d'] = $pengumuman;

        return view('admin.pengumuman.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $pengumuman = MasterPost::where('kategori', 1)->find($id);
        if (!$pengumuman) {
            return redirect('pengumuman')->with('error', 'Data pengumuman tidak ditemukan.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
            'gambar' => 'required|string|max:255',
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
        ]);

        $pengumuman->update([
            'judul' => trim($validated['judul']),
            'isi' => $validated['isi'],
            'gambar' => trim($validated['gambar']),
            'tgl_awal' => $validated['tgl_awal'],
            'tgl_akhir' => $validated['tgl_akhir'],
            'kategori' => 1,
        ]);

        return redirect('pengumuman')->with('status', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pengumuman = MasterPost::where('kategori', 1)->find($id);
        if (!$pengumuman) {
            return redirect('pengumuman')->with('error', 'Data pengumuman tidak ditemukan.');
        }

        $pengumuman->delete();

        return redirect('pengumuman')->with('status', 'Pengumuman berhasil dihapus.');
    }
}
