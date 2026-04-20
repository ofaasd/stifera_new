<?php

namespace App\Http\Controllers;

use App\Models\PegawaiOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PegawaiRiwayatOrganisasiController extends Controller
{
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $riwayat = PegawaiOrganisasi::query()
            ->where('id_pegawai', (int) $pegawai->id)
            ->orderByDesc('id')
            ->get();

        return view('pegawai.riwayat_organisasi.index', [
            'CurrentPage' => 'content',
            'title' => 'Riwayat Organisasi',
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
            'nama_organisasi' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'tahun' => 'required|digits:4',
            'tahun_keluar' => 'nullable|digits:4',
            'sekarang' => 'required|integer|in:0,1',
        ]);

        $validated['id_pegawai'] = (int) $pegawai->id;
        $validated['tahun'] = $validated['tahun'] . '-01-01';

        if (!empty($validated['tahun_keluar'])) {
            $validated['tahun_keluar'] = $validated['tahun_keluar'] . '-01-01';
        }

        PegawaiOrganisasi::create($validated);

        return redirect()->route('pegawai.riwayat-organisasi.index')->with('status', 'Riwayat organisasi berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiOrganisasi::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $validated = $request->validate([
            'nama_organisasi' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'tahun' => 'required|digits:4',
            'tahun_keluar' => 'nullable|digits:4',
            'sekarang' => 'required|integer|in:0,1',
        ]);

        $validated['tahun'] = $validated['tahun'] . '-01-01';

        if (!empty($validated['tahun_keluar'])) {
            $validated['tahun_keluar'] = $validated['tahun_keluar'] . '-01-01';
        }

        $row->update($validated);

        return redirect()->route('pegawai.riwayat-organisasi.index')->with('status', 'Riwayat organisasi berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiOrganisasi::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $row->delete();

        return redirect()->route('pegawai.riwayat-organisasi.index')->with('status', 'Riwayat organisasi berhasil dihapus.');
    }
}
