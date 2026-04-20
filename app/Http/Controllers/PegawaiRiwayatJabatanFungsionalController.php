<?php

namespace App\Http\Controllers;

use App\Models\JabatanFungsional;
use App\Models\PegawaiJabatanFungsional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PegawaiRiwayatJabatanFungsionalController extends Controller
{
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $riwayat = PegawaiJabatanFungsional::query()
            ->from('pegawai_jabatan_fungsional as pjf')
            ->leftJoin('jabatan_fungsional as jf', 'jf.jabatan', '=', 'pjf.jabatan_fungsional_sekarang')
            ->where('pjf.id_pegawai', (int) $pegawai->id)
            ->select('pjf.*', 'jf.jabatan as jabatan_fungsional_master')
            ->orderByDesc('pjf.id')
            ->get();

        $jabatanFungsionalList = JabatanFungsional::query()
            ->orderBy('jabatan')
            ->get(['id', 'jabatan']);

        return view('pegawai.riwayat_jabatan_fungsional.index', [
            'CurrentPage' => 'content',
            'title' => 'Riwayat Jabatan Fungsional',
            'riwayat' => $riwayat,
            'jabatanFungsionalList' => $jabatanFungsionalList,
        ]);
    }

    public function store(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $validated = $request->validate([
            'jabatan_fungsional_sekarang' => 'required|exists:jabatan_fungsional,id',
            'no_sk_fungsional' => 'required|string|max:255',
            'tgl_sk_fungsional' => 'required|date',
            'tmt_sk_fungsional' => 'nullable|date',
            'kum' => 'nullable|string|max:255',
            'status' => 'required|integer|in:0,1',
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ]);

        $validated['id_pegawai'] = (int) $pegawai->id;

        if ($request->hasFile('dokumen') && $request->file('dokumen')->isValid()) {
            File::ensureDirectoryExists(public_path('assets/dokumen_jabatan_fungsional_pegawai'));
            $ext = $request->file('dokumen')->getClientOriginalExtension();
            $fileName = 'jabfung_' . $pegawai->id . '_' . time() . '.' . $ext;
            $request->file('dokumen')->move(public_path('assets/dokumen_jabatan_fungsional_pegawai'), $fileName);
            $validated['dokumen'] = $fileName;
        }

        PegawaiJabatanFungsional::create($validated);

        return redirect()->route('pegawai.riwayat-jabatan-fungsional.index')->with('status', 'Riwayat jabatan fungsional berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiJabatanFungsional::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $validated = $request->validate([
            'jabatan_fungsional_sekarang' => 'required|exists:jabatan_fungsional,id',
            'no_sk_fungsional' => 'required|string|max:255',
            'tgl_sk_fungsional' => 'required|date',
            'tmt_sk_fungsional' => 'nullable|date',
            'kum' => 'nullable|string|max:255',
            'status' => 'required|integer|in:0,1',
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ]);

        if ($request->hasFile('dokumen') && $request->file('dokumen')->isValid()) {
            File::ensureDirectoryExists(public_path('assets/dokumen_jabatan_fungsional_pegawai'));

            if (!empty($row->dokumen)) {
                $oldPath = public_path('assets/dokumen_jabatan_fungsional_pegawai/' . $row->dokumen);
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $ext = $request->file('dokumen')->getClientOriginalExtension();
            $fileName = 'jabfung_' . $pegawai->id . '_' . time() . '.' . $ext;
            $request->file('dokumen')->move(public_path('assets/dokumen_jabatan_fungsional_pegawai'), $fileName);
            $validated['dokumen'] = $fileName;
        }

        $row->update($validated);

        return redirect()->route('pegawai.riwayat-jabatan-fungsional.index')->with('status', 'Riwayat jabatan fungsional berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiJabatanFungsional::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        if (!empty($row->dokumen)) {
            $oldPath = public_path('assets/dokumen_jabatan_fungsional_pegawai/' . $row->dokumen);
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $row->delete();

        return redirect()->route('pegawai.riwayat-jabatan-fungsional.index')->with('status', 'Riwayat jabatan fungsional berhasil dihapus.');
    }
}
