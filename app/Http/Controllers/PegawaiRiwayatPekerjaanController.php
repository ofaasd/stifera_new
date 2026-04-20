<?php

namespace App\Http\Controllers;

use App\Models\PegawaiPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PegawaiRiwayatPekerjaanController extends Controller
{
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $riwayat = PegawaiPekerjaan::query()
            ->where('id_pegawai', (int) $pegawai->id)
            ->orderByDesc('id')
            ->get();

        return view('pegawai.riwayat_pekerjaan.index', [
            'CurrentPage' => 'content',
            'title' => 'Riwayat Pekerjaan',
            'riwayat' => $riwayat,
        ]);
    }

    public function store(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $validated = $request->validate([
            'posisi' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'tahun_masuk' => 'required|digits:4',
            'tahun_keluar' => 'nullable|digits:4',
            'sekarang' => 'required|integer|in:0,1',
        ]);

        $validated['id_pegawai'] = (int) $pegawai->id;
        $validated['tahun_masuk'] = $validated['tahun_masuk'] . '-01-01';

        if (!empty($validated['tahun_keluar'])) {
            $validated['tahun_keluar'] = $validated['tahun_keluar'] . '-01-01';
        }

        PegawaiPekerjaan::create($validated);

        return redirect()->route('pegawai.riwayat-pekerjaan.index')->with('status', 'Riwayat pekerjaan berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiPekerjaan::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $validated = $request->validate([
            'posisi' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'tahun_masuk' => 'required|digits:4',
            'tahun_keluar' => 'nullable|digits:4',
            'sekarang' => 'required|integer|in:0,1',
        ]);

        $validated['tahun_masuk'] = $validated['tahun_masuk'] . '-01-01';

        if (!empty($validated['tahun_keluar'])) {
            $validated['tahun_keluar'] = $validated['tahun_keluar'] . '-01-01';
        }

        $row->update($validated);

        return redirect()->route('pegawai.riwayat-pekerjaan.index')->with('status', 'Riwayat pekerjaan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiPekerjaan::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $row->delete();

        return redirect()->route('pegawai.riwayat-pekerjaan.index')->with('status', 'Riwayat pekerjaan berhasil dihapus.');
    }
}
