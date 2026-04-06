<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Fakulta;
use Illuminate\Http\Request;

class MasterFakultasController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Fakultas';
        $data['CurrentPage'] = 'content';
        $data['fakultasList'] = Fakulta::orderBy('nama_fakultas')->get();

        return view('admin.fakultas.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Fakultas';
        $data['CurrentPage'] = 'content';

        return view('admin.fakultas.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'nullable|string|max:20|unique:fakultas,kode',
            'nama_fakultas' => 'required|string|max:150|unique:fakultas,nama_fakultas',
            'tgl_berdiri' => 'required|date',
            'is_aktif' => 'required|in:0,1',
        ]);

        Fakulta::create([
            'kode' => $validated['kode'] !== null ? strtoupper(trim($validated['kode'])) : null,
            'nama_fakultas' => strtoupper(trim($validated['nama_fakultas'])),
            'tgl_berdiri' => $validated['tgl_berdiri'],
            'is_aktif' => (int) $validated['is_aktif'],
        ]);

        return redirect('master/fakultas')->with('status', 'Data fakultas berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $fakultas = Fakulta::find($id);
        if (!$fakultas) {
            return redirect('master/fakultas')->with('error', 'Data fakultas tidak ditemukan.');
        }

        $data['title'] = 'Edit Fakultas';
        $data['CurrentPage'] = 'content';
        $data['d'] = $fakultas;

        return view('admin.fakultas.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $fakultas = Fakulta::find($id);
        if (!$fakultas) {
            return redirect('master/fakultas')->with('error', 'Data fakultas tidak ditemukan.');
        }

        $validated = $request->validate([
            'kode' => 'nullable|string|max:20|unique:fakultas,kode,' . $fakultas->id,
            'nama_fakultas' => 'required|string|max:150|unique:fakultas,nama_fakultas,' . $fakultas->id,
            'tgl_berdiri' => 'required|date',
            'is_aktif' => 'required|in:0,1',
        ]);

        $fakultas->update([
            'kode' => $validated['kode'] !== null ? strtoupper(trim($validated['kode'])) : null,
            'nama_fakultas' => strtoupper(trim($validated['nama_fakultas'])),
            'tgl_berdiri' => $validated['tgl_berdiri'],
            'is_aktif' => (int) $validated['is_aktif'],
        ]);

        return redirect('master/fakultas')->with('status', 'Data fakultas berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $fakultas = Fakulta::find($id);
        if (!$fakultas) {
            return redirect('master/fakultas')->with('error', 'Data fakultas tidak ditemukan.');
        }

        $fakultas->delete();

        return redirect('master/fakultas')->with('status', 'Data fakultas berhasil dihapus.');
    }
}
