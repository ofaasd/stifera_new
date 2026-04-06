<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterRumpun;
use Illuminate\Http\Request;

class MasterRumpunController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Rumpun';
        $data['CurrentPage'] = 'content';
        $data['rumpunList'] = MasterRumpun::orderBy('nama_rumpun')->get();

        return view('admin.rumpun.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Rumpun';
        $data['CurrentPage'] = 'content';

        return view('admin.rumpun.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_rumpun' => 'required|string|max:100|unique:master_rumpun,nama_rumpun',
            'status' => 'required|in:0,1',
        ]);

        MasterRumpun::create([
            'nama_rumpun' => strtoupper(trim($validated['nama_rumpun'])),
            'status' => (int) $validated['status'],
        ]);

        return redirect('master/rumpun')->with('status', 'Data rumpun berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $rumpun = MasterRumpun::find($id);
        if (!$rumpun) {
            return redirect('master/rumpun')->with('error', 'Data rumpun tidak ditemukan.');
        }

        $data['title'] = 'Edit Rumpun';
        $data['CurrentPage'] = 'content';
        $data['d'] = $rumpun;

        return view('admin.rumpun.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $rumpun = MasterRumpun::find($id);
        if (!$rumpun) {
            return redirect('master/rumpun')->with('error', 'Data rumpun tidak ditemukan.');
        }

        $validated = $request->validate([
            'nama_rumpun' => 'required|string|max:100|unique:master_rumpun,nama_rumpun,' . $rumpun->id,
            'status' => 'required|in:0,1',
        ]);

        $rumpun->update([
            'nama_rumpun' => strtoupper(trim($validated['nama_rumpun'])),
            'status' => (int) $validated['status'],
        ]);

        return redirect('master/rumpun')->with('status', 'Data rumpun berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $rumpun = MasterRumpun::find($id);
        if (!$rumpun) {
            return redirect('master/rumpun')->with('error', 'Data rumpun tidak ditemukan.');
        }

        $rumpun->delete();

        return redirect('master/rumpun')->with('status', 'Data rumpun berhasil dihapus.');
    }
}
