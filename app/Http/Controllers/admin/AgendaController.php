<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterPost;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index()
    {
        $data['title'] = 'Agenda';
        $data['CurrentPage'] = 'content';
        $data['agendaList'] = MasterPost::where('kategori', 2)
            ->orderByDesc('id')
            ->get();

        return view('admin.agenda.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Agenda';
        $data['CurrentPage'] = 'content';

        return view('admin.agenda.create', $data);
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
            'kategori' => 2,
        ]);

        return redirect()->route('agenda.index')->with('status', 'Agenda berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $agenda = MasterPost::where('kategori', 2)->find($id);
        if (!$agenda) {
            return redirect()->route('agenda.index')->with('error', 'Data agenda tidak ditemukan.');
        }

        $data['title'] = 'Edit Agenda';
        $data['CurrentPage'] = 'content';
        $data['d'] = $agenda;

        return view('admin.agenda.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $agenda = MasterPost::where('kategori', 2)->find($id);
        if (!$agenda) {
            return redirect()->route('agenda.index')->with('error', 'Data agenda tidak ditemukan.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
            'gambar' => 'required|string|max:255',
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
        ]);

        $agenda->update([
            'judul' => trim($validated['judul']),
            'isi' => $validated['isi'],
            'gambar' => trim($validated['gambar']),
            'tgl_awal' => $validated['tgl_awal'],
            'tgl_akhir' => $validated['tgl_akhir'],
            'kategori' => 2,
        ]);

        return redirect()->route('agenda.index')->with('status', 'Agenda berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $agenda = MasterPost::where('kategori', 2)->find($id);
        if (!$agenda) {
            return redirect()->route('agenda.index')->with('error', 'Data agenda tidak ditemukan.');
        }

        $agenda->delete();

        return redirect()->route('agenda.index')->with('status', 'Agenda berhasil dihapus.');
    }
}
