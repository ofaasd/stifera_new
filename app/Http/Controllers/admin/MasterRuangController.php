<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterRuang;
use Illuminate\Http\Request;

class MasterRuangController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Ruang';
        $data['CurrentPage'] = 'content';
        $data['ruangList'] = MasterRuang::orderBy('nama_ruang')->get();

        return view('admin.ruang.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Ruang';
        $data['CurrentPage'] = 'content';

        return view('admin.ruang.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ruang' => 'required|string|max:100|unique:master_ruang,nama_ruang',
            'kapasitas' => 'required|integer|min:1',
            'luas' => 'required|string|max:50',
        ]);

        MasterRuang::create([
            'nama_ruang' => strtoupper(trim($validated['nama_ruang'])),
            'kapasitas' => (int) $validated['kapasitas'],
            'luas' => trim($validated['luas']),
            'log_update' => now(),
        ]);

        return redirect('master/ruang')->with('status', 'Data ruang berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $ruang = MasterRuang::find($id);
        if (!$ruang) {
            return redirect('master/ruang')->with('error', 'Data ruang tidak ditemukan.');
        }

        $data['title'] = 'Edit Ruang';
        $data['CurrentPage'] = 'content';
        $data['d'] = $ruang;

        return view('admin.ruang.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $ruang = MasterRuang::find($id);
        if (!$ruang) {
            return redirect('master/ruang')->with('error', 'Data ruang tidak ditemukan.');
        }

        $validated = $request->validate([
            'nama_ruang' => 'required|string|max:100|unique:master_ruang,nama_ruang,' . $ruang->id,
            'kapasitas' => 'required|integer|min:1',
            'luas' => 'required|string|max:50',
        ]);

        $ruang->update([
            'nama_ruang' => strtoupper(trim($validated['nama_ruang'])),
            'kapasitas' => (int) $validated['kapasitas'],
            'luas' => trim($validated['luas']),
            'log_update' => now(),
        ]);

        return redirect('master/ruang')->with('status', 'Data ruang berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $ruang = MasterRuang::find($id);
        if (!$ruang) {
            return redirect('master/ruang')->with('error', 'Data ruang tidak ditemukan.');
        }

        $ruang->delete();

        return redirect('master/ruang')->with('status', 'Data ruang berhasil dihapus.');
    }
}
