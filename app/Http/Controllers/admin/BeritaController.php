<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterPost;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index()
    {
        $data['title'] = 'Berita';
        $data['CurrentPage'] = 'content';
        $data['beritaList'] = MasterPost::where('kategori', 3)
            ->orderByDesc('id')
            ->get();

        return view('admin.berita.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Berita';
        $data['CurrentPage'] = 'content';

        return view('admin.berita.create', $data);
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
            'kategori' => 3,
        ]);

        return redirect()->route('berita.index')->with('status', 'Berita berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $berita = MasterPost::where('kategori', 3)->find($id);
        if (!$berita) {
            return redirect()->route('berita.index')->with('error', 'Data berita tidak ditemukan.');
        }

        $data['title'] = 'Edit Berita';
        $data['CurrentPage'] = 'content';
        $data['d'] = $berita;

        return view('admin.berita.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $berita = MasterPost::where('kategori', 3)->find($id);
        if (!$berita) {
            return redirect()->route('berita.index')->with('error', 'Data berita tidak ditemukan.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
            'gambar' => 'required|string|max:255',
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
        ]);

        $berita->update([
            'judul' => trim($validated['judul']),
            'isi' => $validated['isi'],
            'gambar' => trim($validated['gambar']),
            'tgl_awal' => $validated['tgl_awal'],
            'tgl_akhir' => $validated['tgl_akhir'],
            'kategori' => 3,
        ]);

        return redirect()->route('berita.index')->with('status', 'Berita berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $berita = MasterPost::where('kategori', 3)->find($id);
        if (!$berita) {
            return redirect()->route('berita.index')->with('error', 'Data berita tidak ditemukan.');
        }

        $berita->delete();

        return redirect()->route('berita.index')->with('status', 'Berita berhasil dihapus.');
    }
}
