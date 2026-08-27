<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterTahunAjaran;
use Illuminate\Http\Request;

class MasterTahunAjaranController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Tahun Ajaran';
        $data['CurrentPage'] = 'content';
        $data['tahunList'] = MasterTahunAjaran::orderByDesc('id_tahun')->orderByDesc('id')->get();

        return view('admin.tahun.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Tahun Ajaran';
        $data['CurrentPage'] = 'content';

        return view('admin.tahun.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_tahun' => 'required|integer|unique:master_tahun_ajaran,id_tahun',
            'awal' => 'required|string|max:10',
            'akhir' => 'required|string|max:10',
            'jenis' => 'required|integer|in:1,2',
            'is_delete' => 'nullable|string|max:10',
            'is_aktif' => 'required|integer|in:0,1',
            'status' => 'required|integer|in:0,1',
            'tipe_mhs' => 'required|integer|in:1,2',
            'kuesioner' => 'required|integer|in:0,1',
        ]);

        MasterTahunAjaran::create([
            'id_tahun' => (int) $validated['id_tahun'],
            'awal' => trim($validated['awal']),
            'akhir' => trim($validated['akhir']),
            'jenis' => (int) $validated['jenis'],
            'is_delete' => $validated['is_delete'] !== null ? trim($validated['is_delete']) : null,
            'is_aktif' => (int) $validated['is_aktif'],
            'status' => (int) $validated['status'],
            'tipe_mhs' => (int) $validated['tipe_mhs'],
            'kuesioner' => (int) $validated['kuesioner'],
        ]);

        return redirect('master/tahun')->with('status', 'Data tahun ajaran berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $tahun = MasterTahunAjaran::find($id);
        if (!$tahun) {
            return redirect('master/tahun')->with('error', 'Data tahun ajaran tidak ditemukan.');
        }

        $data['title'] = 'Edit Tahun Ajaran';
        $data['CurrentPage'] = 'content';
        $data['d'] = $tahun;

        return view('admin.tahun.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $tahun = MasterTahunAjaran::find($id);
        if (!$tahun) {
            return redirect('master/tahun')->with('error', 'Data tahun ajaran tidak ditemukan.');
        }

        $validated = $request->validate([
            'id_tahun' => 'required|integer|unique:master_tahun_ajaran,id_tahun,' . $tahun->id,
            'awal' => 'required|string|max:10',
            'akhir' => 'required|string|max:10',
            'jenis' => 'required|integer|in:1,2',
            'is_delete' => 'nullable|string|max:10',
            'is_aktif' => 'required|integer|in:0,1',
            'status' => 'required|integer|in:0,1',
            'tipe_mhs' => 'required|integer|in:1,2',
            'kuesioner' => 'required|integer|in:0,1',
        ]);

        $tahun->update([
            'id_tahun' => (int) $validated['id_tahun'],
            'awal' => trim($validated['awal']),
            'akhir' => trim($validated['akhir']),
            'jenis' => (int) $validated['jenis'],
            'is_delete' => $validated['is_delete'] !== null ? trim($validated['is_delete']) : null,
            'is_aktif' => (int) $validated['is_aktif'],
            'status' => (int) $validated['status'],
            'tipe_mhs' => (int) $validated['tipe_mhs'],
            'kuesioner' => (int) $validated['kuesioner'],
        ]);

        return redirect('master/tahun')->with('status', 'Data tahun ajaran berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $tahun = MasterTahunAjaran::find($id);
        if (!$tahun) {
            return redirect('master/tahun')->with('error', 'Data tahun ajaran tidak ditemukan.');
        }

        $tahun->delete();

        return redirect('master/tahun')->with('status', 'Data tahun ajaran berhasil dihapus.');
    }
}
