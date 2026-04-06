<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterJam;
use Illuminate\Http\Request;

class MasterWaktuController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Waktu';
        $data['CurrentPage'] = 'content';
        $data['waktuList'] = MasterJam::orderBy('id')->get();

        return view('admin.waktu.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Waktu';
        $data['CurrentPage'] = 'content';

        return view('admin.waktu.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sesi' => 'required|string|max:100|unique:master_jam,nama_sesi',
            'mulai' => 'required|date_format:H:i',
            'selesai' => 'required|date_format:H:i',
            'sks' => 'required|integer|min:1|max:10',
            'status' => 'required|in:0,1',
        ]);

        if (strtotime($validated['selesai']) <= strtotime($validated['mulai'])) {
            return back()->withErrors([
                'selesai' => 'Jam selesai harus lebih besar dari jam mulai.',
            ])->withInput();
        }

        MasterJam::create([
            'nama_sesi' => trim($validated['nama_sesi']),
            'mulai' => $validated['mulai'] . ':00',
            'selesai' => $validated['selesai'] . ':00',
            'sks' => (int) $validated['sks'],
            'status' => (int) $validated['status'],
        ]);

        return redirect('master/waktu')->with('status', 'Data waktu berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $waktu = MasterJam::find($id);
        if (!$waktu) {
            return redirect('master/waktu')->with('error', 'Data waktu tidak ditemukan.');
        }

        $data['title'] = 'Edit Waktu';
        $data['CurrentPage'] = 'content';
        $data['d'] = $waktu;

        return view('admin.waktu.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $waktu = MasterJam::find($id);
        if (!$waktu) {
            return redirect('master/waktu')->with('error', 'Data waktu tidak ditemukan.');
        }

        $validated = $request->validate([
            'nama_sesi' => 'required|string|max:100|unique:master_jam,nama_sesi,' . $waktu->id,
            'mulai' => 'required|date_format:H:i',
            'selesai' => 'required|date_format:H:i',
            'sks' => 'required|integer|min:1|max:10',
            'status' => 'required|in:0,1',
        ]);

        if (strtotime($validated['selesai']) <= strtotime($validated['mulai'])) {
            return back()->withErrors([
                'selesai' => 'Jam selesai harus lebih besar dari jam mulai.',
            ])->withInput();
        }

        $waktu->update([
            'nama_sesi' => trim($validated['nama_sesi']),
            'mulai' => $validated['mulai'] . ':00',
            'selesai' => $validated['selesai'] . ':00',
            'sks' => (int) $validated['sks'],
            'status' => (int) $validated['status'],
        ]);

        return redirect('master/waktu')->with('status', 'Data waktu berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $waktu = MasterJam::find($id);
        if (!$waktu) {
            return redirect('master/waktu')->with('error', 'Data waktu tidak ditemukan.');
        }

        $waktu->delete();

        return redirect('master/waktu')->with('status', 'Data waktu berhasil dihapus.');
    }
}
