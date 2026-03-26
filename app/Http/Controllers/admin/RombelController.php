<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TblRombel;
use Illuminate\Http\Request;

class RombelController extends Controller
{
    public function index()
    {
        $data['title'] = 'Daftar Rombel';
        $data['CurrentPage'] = 'content';
        $data['rombel'] = TblRombel::orderByDesc('id')->get();

        return view('admin.rombel.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Rombel';
        $data['CurrentPage'] = 'content';

        return view('admin.rombel.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rombel' => 'required|string|max:50|unique:tbl_rombel,rombel',
            'is_aktif' => 'required|in:Aktif,Tidak Aktif',
        ]);

        TblRombel::create([
            'rombel' => strtoupper(trim($validated['rombel'])),
            'is_aktif' => $validated['is_aktif'],
            'create_on' => now(),
        ]);

        return redirect('master/rombel')->with('status', 'Data rombel berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $rombel = TblRombel::find($id);
        if (!$rombel) {
            return redirect('master/rombel')->with('error', 'Data rombel tidak ditemukan.');
        }

        $data['title'] = 'Edit Rombel';
        $data['CurrentPage'] = 'content';
        $data['d'] = $rombel;

        return view('admin.rombel.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $rombel = TblRombel::find($id);
        if (!$rombel) {
            return redirect('master/rombel')->with('error', 'Data rombel tidak ditemukan.');
        }

        $validated = $request->validate([
            'rombel' => 'required|string|max:50|unique:tbl_rombel,rombel,' . $rombel->id,
            'is_aktif' => 'required|in:Aktif,Tidak Aktif',
        ]);

        $rombel->update([
            'rombel' => strtoupper(trim($validated['rombel'])),
            'is_aktif' => $validated['is_aktif'],
        ]);

        return redirect('master/rombel')->with('status', 'Data rombel berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $rombel = TblRombel::find($id);
        if (!$rombel) {
            return redirect('master/rombel')->with('error', 'Data rombel tidak ditemukan.');
        }

        $rombel->delete();

        return redirect('master/rombel')->with('status', 'Data rombel berhasil dihapus.');
    }
}
