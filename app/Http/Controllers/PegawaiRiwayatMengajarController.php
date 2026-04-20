<?php

namespace App\Http\Controllers;

use App\Models\PegawaiMengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PegawaiRiwayatMengajarController extends Controller
{
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $riwayat = PegawaiMengajar::query()
            ->where('id_pegawai', (int) $pegawai->id)
            ->orderByDesc('tahun')
            ->orderByDesc('id')
            ->get();

        foreach ($riwayat as $row) {
            $row->dokumen_url = $this->resolveDocumentUrl($row->dokumen, 'dokumen');
            $row->sk_mengajar_url = $this->resolveDocumentUrl($row->sk_mengajar, 'sk_mengajar');
        }

        $riwayatKrs = collect();

        $biodataId = DB::table('pegawai_biodata')
            ->where('id_pegawai', (int) $pegawai->id)
            ->value('id');

        if ($biodataId) {
            $riwayatKrs = DB::table('master_jadwal as mj')
                ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mj.kode_mata_kuliah')
                ->leftJoin('master_tahun_ajaran as mta', 'mta.id', '=', 'mj.id_tahun')
                ->leftJoin('program_studi as mps', 'mps.id', '=', 'mmk.id_program_studi')
                ->where(function ($q) use ($biodataId) {
                    $q->where('mj.id_dosen', (int) $biodataId)
                      ->orWhere('mj.id_dosen2', (int) $biodataId);
                })
                ->select(
                    'mj.id',
                    'mj.kode_jadwal',
                    'mj.kode_mata_kuliah',
                    'mj.rombel',
                    'mj.kelas',
                    'mta.id_tahun',
                    'mj.rps',
                    'mj.kp',
                    'mmk.nama_mata_kuliah',
                    'mmk.jumlah_sks',
                    'mps.nama_jurusan as nama_prodi',
                    'mta.awal as tahun_awal',
                    'mta.akhir as tahun_akhir',
                    'mta.jenis as tahun_jenis'
                )
                ->orderByDesc('mj.id_tahun')
                ->orderByDesc('mj.id')
                ->get();
        }

        return view('pegawai.riwayat_mengajar.index', [
            'CurrentPage' => 'content',
            'title' => 'Riwayat Mengajar',
            'riwayat' => $riwayat,
            'riwayatKrs' => $riwayatKrs,
        ]);
    }

    public function store(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $validated = $request->validate([
            'tahun' => 'required|integer|min:1900|max:2099',
            'institusi' => 'required|string|max:255',
            'prodi' => 'required|string|max:255',
            'mata_kuliah' => 'required|string|max:255',
            'rombel' => 'nullable|string|max:100',
            'kelas' => 'nullable|integer|min:0',
            'sks' => 'nullable|integer|min:0',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'sk_mengajar' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('dokumen')) {
            $validated['dokumen'] = $request->file('dokumen')->store('pegawai/mengajar/dokumen', 'public');
        }

        if ($request->hasFile('sk_mengajar')) {
            $validated['sk_mengajar'] = $request->file('sk_mengajar')->store('pegawai/mengajar/sk_mengajar', 'public');
        }

        $validated['id_pegawai'] = (int) $pegawai->id;

        PegawaiMengajar::create($validated);

        return redirect()->route('pegawai.riwayat-mengajar.index')->with('status', 'Riwayat mengajar berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiMengajar::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $validated = $request->validate([
            'tahun' => 'required|integer|min:1900|max:2099',
            'institusi' => 'required|string|max:255',
            'prodi' => 'required|string|max:255',
            'mata_kuliah' => 'required|string|max:255',
            'rombel' => 'nullable|string|max:100',
            'kelas' => 'nullable|integer|min:0',
            'sks' => 'nullable|integer|min:0',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'sk_mengajar' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('dokumen')) {
            $oldDokumen = $row->dokumen;
            $validated['dokumen'] = $request->file('dokumen')->store('pegawai/mengajar/dokumen', 'public');
            $this->deleteStoredDocumentIfExists($oldDokumen);
        } else {
            unset($validated['dokumen']);
        }

        if ($request->hasFile('sk_mengajar')) {
            $oldSkMengajar = $row->sk_mengajar;
            $validated['sk_mengajar'] = $request->file('sk_mengajar')->store('pegawai/mengajar/sk_mengajar', 'public');
            $this->deleteStoredDocumentIfExists($oldSkMengajar);
        } else {
            unset($validated['sk_mengajar']);
        }

        $row->update($validated);

        return redirect()->route('pegawai.riwayat-mengajar.index')->with('status', 'Riwayat mengajar berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiMengajar::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $this->deleteStoredDocumentIfExists($row->dokumen);
        $this->deleteStoredDocumentIfExists($row->sk_mengajar);

        $row->delete();

        return redirect()->route('pegawai.riwayat-mengajar.index')->with('status', 'Riwayat mengajar berhasil dihapus.');
    }

    private function resolveDocumentUrl(?string $value, string $type): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = str_replace('\\', '/', trim($value));

        if (preg_match('/^https?:\/\//i', $value) === 1) {
            return $value;
        }

        $publicCandidates = [
            $value,
            'assets/mengajar/' . ltrim($value, '/'),
            'uploads/mengajar/' . ltrim($value, '/'),
        ];

        foreach ($publicCandidates as $candidate) {
            $candidate = trim($candidate, '/');
            if ($candidate !== '' && is_file(public_path($candidate))) {
                return asset($candidate);
            }
        }

        if (Storage::disk('public')->exists($value)) {
            return asset('storage/' . ltrim($value, '/'));
        }

        $folderType = $type === 'sk_mengajar' ? 'sk_mengajar' : 'dokumen';
        $storageCandidates = [
            'pegawai/mengajar/' . ltrim($value, '/'),
            'pegawai/mengajar/' . $folderType . '/' . ltrim($value, '/'),
        ];

        foreach ($storageCandidates as $candidate) {
            if (Storage::disk('public')->exists($candidate)) {
                return asset('storage/' . ltrim($candidate, '/'));
            }
        }

        return null;
    }

    private function deleteStoredDocumentIfExists(?string $value): void
    {
        if (empty($value)) {
            return;
        }

        $value = str_replace('\\', '/', trim($value));

        if (preg_match('/^https?:\/\//i', $value) === 1) {
            return;
        }

        $candidates = [
            $value,
            'pegawai/mengajar/dokumen/' . ltrim($value, '/'),
            'pegawai/mengajar/sk_mengajar/' . ltrim($value, '/'),
        ];

        foreach ($candidates as $candidate) {
            $candidate = trim($candidate, '/');
            if ($candidate !== '' && Storage::disk('public')->exists($candidate)) {
                Storage::disk('public')->delete($candidate);
                break;
            }
        }
    }
}
