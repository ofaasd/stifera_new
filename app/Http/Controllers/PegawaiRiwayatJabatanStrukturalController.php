<?php

namespace App\Http\Controllers;

use App\Models\JabatanStruktural;
use App\Models\PegawaiJabatanStruktural;
use App\Models\PegawaiUnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PegawaiRiwayatJabatanStrukturalController extends Controller
{
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $riwayat = PegawaiJabatanStruktural::query()
            ->from('pegawai_jabatan_struktural as pjs')
            ->leftJoin('pegawai_unit_kerja as puk', 'puk.id', '=', 'pjs.unit_kerja')
            ->leftJoin('jabatan_struktural as js', 'js.id', '=', 'pjs.id_jabatan_struktural')
            ->where('pjs.id_pegawai', (int) $pegawai->id)
            ->select('pjs.*', 'puk.unit_kerja as unit_kerja_nama', 'js.jabatan as jabatan_struktural_nama', 'js.bagian as jabatan_struktural_bagian')
            ->orderByDesc('pjs.id')
            ->get();

        $unitKerjaList = PegawaiUnitKerja::query()
            ->orderBy('unit_kerja')
            ->get(['id', 'unit_kerja']);

        $jabatanStrukturalList = JabatanStruktural::query()
            ->orderBy('jabatan')
            ->get(['id', 'jabatan', 'bagian']);

        return view('pegawai.riwayat_jabatan_struktural.index', [
            'CurrentPage' => 'content',
            'title' => 'Riwayat Jabatan Struktural',
            'riwayat' => $riwayat,
            'unitKerjaList' => $unitKerjaList,
            'jabatanStrukturalList' => $jabatanStrukturalList,
        ]);
    }

    public function store(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $validated = $request->validate([
            'unit_kerja' => 'required|integer|exists:pegawai_unit_kerja,id',
            'id_jabatan_struktural' => 'required|integer|exists:jabatan_struktural,id',
            'no_sk_struktural' => 'required|string|max:255',
            'tanggal_sk_struktural' => 'required|date',
            'tmt_sk_struktural' => 'required|date',
            'status' => 'required|integer|in:0,1',
            'tahun_masuk' => 'nullable|digits:4',
            'tahun_keluar' => 'nullable|digits:4',
            'sekarang' => 'required|integer|in:0,1',
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ]);

        $validated['id_pegawai'] = (int) $pegawai->id;

        if ($request->hasFile('dokumen') && $request->file('dokumen')->isValid()) {
            File::ensureDirectoryExists(public_path('assets/dokumen_jabatan_struktural_pegawai'));
            $ext = $request->file('dokumen')->getClientOriginalExtension();
            $fileName = 'jabstruk_' . $pegawai->id . '_' . time() . '.' . $ext;
            $request->file('dokumen')->move(public_path('assets/dokumen_jabatan_struktural_pegawai'), $fileName);
            $validated['dokumen'] = $fileName;
        }

        if (!empty($validated['tahun_masuk'])) {
            $validated['tahun_masuk'] = $validated['tahun_masuk'] . '-01-01';
        }

        if (!empty($validated['tahun_keluar'])) {
            $validated['tahun_keluar'] = $validated['tahun_keluar'] . '-01-01';
        }

        PegawaiJabatanStruktural::create($validated);

        return redirect()->route('pegawai.riwayat-jabatan-struktural.index')->with('status', 'Riwayat jabatan struktural berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiJabatanStruktural::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $validated = $request->validate([
            'unit_kerja' => 'required|integer|exists:pegawai_unit_kerja,id',
            'id_jabatan_struktural' => 'required|integer|exists:jabatan_struktural,id',
            'no_sk_struktural' => 'required|string|max:255',
            'tanggal_sk_struktural' => 'required|date',
            'tmt_sk_struktural' => 'required|date',
            'status' => 'required|integer|in:0,1',
            'tahun_masuk' => 'nullable|digits:4',
            'tahun_keluar' => 'nullable|digits:4',
            'sekarang' => 'required|integer|in:0,1',
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ]);

        if ($request->hasFile('dokumen') && $request->file('dokumen')->isValid()) {
            File::ensureDirectoryExists(public_path('assets/dokumen_jabatan_struktural_pegawai'));

            if (!empty($row->dokumen)) {
                $oldPath = public_path('assets/dokumen_jabatan_struktural_pegawai/' . $row->dokumen);
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $ext = $request->file('dokumen')->getClientOriginalExtension();
            $fileName = 'jabstruk_' . $pegawai->id . '_' . time() . '.' . $ext;
            $request->file('dokumen')->move(public_path('assets/dokumen_jabatan_struktural_pegawai'), $fileName);
            $validated['dokumen'] = $fileName;
        }

        if (!empty($validated['tahun_masuk'])) {
            $validated['tahun_masuk'] = $validated['tahun_masuk'] . '-01-01';
        }

        if (!empty($validated['tahun_keluar'])) {
            $validated['tahun_keluar'] = $validated['tahun_keluar'] . '-01-01';
        }

        $row->update($validated);

        return redirect()->route('pegawai.riwayat-jabatan-struktural.index')->with('status', 'Riwayat jabatan struktural berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiJabatanStruktural::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        if (!empty($row->dokumen)) {
            $oldPath = public_path('assets/dokumen_jabatan_struktural_pegawai/' . $row->dokumen);
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $row->delete();

        return redirect()->route('pegawai.riwayat-jabatan-struktural.index')->with('status', 'Riwayat jabatan struktural berhasil dihapus.');
    }
}
