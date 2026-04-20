<?php

namespace App\Http\Controllers;

use App\Models\PegawaiBuku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PegawaiRiwayatBukuController extends Controller
{
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $riwayat = PegawaiBuku::query()
            ->where('id_pegawai', (int) $pegawai->id)
            ->orderByDesc('id')
            ->get();

        foreach ($riwayat as $row) {
            $row->dokumen_url = $this->resolveDocumentUrl($row->link_dokumen);
        }

        return view('pegawai.riwayat_buku.index', [
            'CurrentPage' => 'content',
            'title' => 'Riwayat Buku',
            'riwayat' => $riwayat,
        ]);
    }

    public function store(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $validated = $this->validateData($request);
        $validated['id_pegawai'] = (int) $pegawai->id;

        if ($request->hasFile('link_dokumen')) {
            $validated['link_dokumen'] = $request->file('link_dokumen')->store('buku', 'public');
        }

        PegawaiBuku::create($validated);

        return redirect()->route('pegawai.riwayat-buku.index')->with('status', 'Riwayat buku berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiBuku::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $validated = $this->validateData($request);

        if ($request->hasFile('link_dokumen')) {
            $this->deleteStoredFileIfExists($row->link_dokumen);
            $validated['link_dokumen'] = $request->file('link_dokumen')->store('buku', 'public');
        } else {
            unset($validated['link_dokumen']);
        }

        $row->update($validated);

        return redirect()->route('pegawai.riwayat-buku.index')->with('status', 'Riwayat buku berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiBuku::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $this->deleteStoredFileIfExists($row->link_dokumen);

        $row->delete();

        return redirect()->route('pegawai.riwayat-buku.index')->with('status', 'Riwayat buku berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'judul_buku'    => 'required|string|max:255',
            'penulis'       => 'nullable|string|max:255',
            'isbn'          => 'nullable|string|max:50',
            'tahun'         => 'required|digits:4',
            'link_dokumen'  => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);
    }

    private function deleteStoredFileIfExists(?string $path): void
    {
        if (!empty($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function resolveDocumentUrl(?string $value): ?string
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
            'assets/buku/' . ltrim($value, '/'),
            'uploads/buku/' . ltrim($value, '/'),
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

        if (Storage::disk('public')->exists('buku/' . ltrim($value, '/'))) {
            return asset('storage/buku/' . ltrim($value, '/'));
        }

        return null;
    }
}
