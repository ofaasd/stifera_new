<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\JamKerjaMaster;
use Illuminate\Http\Request;

class JamKerjaMasterController extends Controller
{
    public function index()
    {
        $data['title'] = 'Jam Kerja Dosen';
        $data['CurrentPage'] = 'content';
        $data['jamKerjaList'] = JamKerjaMaster::orderBy('id')->get();

        return view('admin.jam_kerja_master.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Jam Kerja Dosen';
        $data['CurrentPage'] = 'content';

        return view('admin.jam_kerja_master.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'mulai' => 'required|date',
            'selesai' => 'required|date',
            'status' => 'required|in:0,1',
        ]);

        if (strtotime($validated['selesai']) < strtotime($validated['mulai'])) {
            return back()->withErrors([
                'selesai' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            ])->withInput();
        }

        JamKerjaMaster::create([
            'judul' => trim($validated['judul']),
            'mulai' => $validated['mulai'],
            'selesai' => $validated['selesai'],
            'status' => (int) $validated['status'],
        ]);

        return redirect('simpeg/absensi/jam_kerja_master')->with('status', 'Data jam kerja dosen berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $jamKerja = JamKerjaMaster::find($id);
        if (!$jamKerja) {
            return redirect('simpeg/absensi/jam_kerja_master')->with('error', 'Data jam kerja dosen tidak ditemukan.');
        }

        $data['title'] = 'Edit Jam Kerja Dosen';
        $data['CurrentPage'] = 'content';
        $data['d'] = $jamKerja;

        return view('admin.jam_kerja_master.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $jamKerja = JamKerjaMaster::find($id);
        if (!$jamKerja) {
            return redirect('simpeg/absensi/jam_kerja_master')->with('error', 'Data jam kerja dosen tidak ditemukan.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'mulai' => 'required|date',
            'selesai' => 'required|date',
            'status' => 'required|in:0,1',
        ]);

        if (strtotime($validated['selesai']) < strtotime($validated['mulai'])) {
            return back()->withErrors([
                'selesai' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            ])->withInput();
        }

        $jamKerja->update([
            'judul' => trim($validated['judul']),
            'mulai' => $validated['mulai'],
            'selesai' => $validated['selesai'],
            'status' => (int) $validated['status'],
        ]);

        return redirect('simpeg/absensi/jam_kerja_master')->with('status', 'Data jam kerja dosen berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $jamKerja = JamKerjaMaster::find($id);
        if (!$jamKerja) {
            return redirect('simpeg/absensi/jam_kerja_master')->with('error', 'Data jam kerja dosen tidak ditemukan.');
        }

        $jamKerja->delete();

        return redirect('simpeg/absensi/jam_kerja_master')->with('status', 'Data jam kerja dosen berhasil dihapus.');
    }
}
