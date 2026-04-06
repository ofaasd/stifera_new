<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Fakulta;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class MasterProgdiController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Program Studi';
        $data['CurrentPage'] = 'content';
        $data['progdiList'] = ProgramStudi::query()
            ->leftJoin('fakultas', 'program_studi.fakultas', '=', 'fakultas.id')
            ->select('program_studi.*', 'fakultas.nama_fakultas')
            ->orderBy('program_studi.nama_jurusan')
            ->get();

        return view('admin.progdi.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Program Studi';
        $data['CurrentPage'] = 'content';
        $data['fakultasList'] = Fakulta::orderBy('nama_fakultas')->get();

        return view('admin.progdi.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'nullable|string|max:20|unique:program_studi,kode',
            'kodenim' => 'required|string|max:20|unique:program_studi,kodenim',
            'jenjang' => 'nullable|string|max:20',
            'nama_jurusan' => 'required|string|max:150|unique:program_studi,nama_jurusan',
            'nama_ijazah' => 'required|string|max:200',
            'nama_ijazah_eng' => 'required|string|max:200',
            'fakultas' => 'nullable|integer|exists:fakultas,id',
            'off' => 'required|in:0,1',
        ]);

        ProgramStudi::create([
            'kode' => $validated['kode'] !== null ? strtoupper(trim($validated['kode'])) : null,
            'kodenim' => strtoupper(trim($validated['kodenim'])),
            'jenjang' => $validated['jenjang'] !== null ? strtoupper(trim($validated['jenjang'])) : null,
            'nama_jurusan' => strtoupper(trim($validated['nama_jurusan'])),
            'nama_ijazah' => trim($validated['nama_ijazah']),
            'nama_ijazah_eng' => trim($validated['nama_ijazah_eng']),
            'fakultas' => $validated['fakultas'] ?? null,
            'off' => (int) $validated['off'],
        ]);

        return redirect('master/progdi')->with('status', 'Data program studi berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $progdi = ProgramStudi::find($id);
        if (!$progdi) {
            return redirect('master/progdi')->with('error', 'Data program studi tidak ditemukan.');
        }

        $data['title'] = 'Edit Program Studi';
        $data['CurrentPage'] = 'content';
        $data['d'] = $progdi;
        $data['fakultasList'] = Fakulta::orderBy('nama_fakultas')->get();

        return view('admin.progdi.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $progdi = ProgramStudi::find($id);
        if (!$progdi) {
            return redirect('master/progdi')->with('error', 'Data program studi tidak ditemukan.');
        }

        $validated = $request->validate([
            'kode' => 'nullable|string|max:20|unique:program_studi,kode,' . $progdi->id,
            'kodenim' => 'required|string|max:20|unique:program_studi,kodenim,' . $progdi->id,
            'jenjang' => 'nullable|string|max:20',
            'nama_jurusan' => 'required|string|max:150|unique:program_studi,nama_jurusan,' . $progdi->id,
            'nama_ijazah' => 'required|string|max:200',
            'nama_ijazah_eng' => 'required|string|max:200',
            'fakultas' => 'nullable|integer|exists:fakultas,id',
            'off' => 'required|in:0,1',
        ]);

        $progdi->update([
            'kode' => $validated['kode'] !== null ? strtoupper(trim($validated['kode'])) : null,
            'kodenim' => strtoupper(trim($validated['kodenim'])),
            'jenjang' => $validated['jenjang'] !== null ? strtoupper(trim($validated['jenjang'])) : null,
            'nama_jurusan' => strtoupper(trim($validated['nama_jurusan'])),
            'nama_ijazah' => trim($validated['nama_ijazah']),
            'nama_ijazah_eng' => trim($validated['nama_ijazah_eng']),
            'fakultas' => $validated['fakultas'] ?? null,
            'off' => (int) $validated['off'],
        ]);

        return redirect('master/progdi')->with('status', 'Data program studi berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $progdi = ProgramStudi::find($id);
        if (!$progdi) {
            return redirect('master/progdi')->with('error', 'Data program studi tidak ditemukan.');
        }

        $progdi->delete();

        return redirect('master/progdi')->with('status', 'Data program studi berhasil dihapus.');
    }
}
