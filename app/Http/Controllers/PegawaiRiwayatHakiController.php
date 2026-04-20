<?php

namespace App\Http\Controllers;

use App\Models\PegawaiHaki;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PegawaiRiwayatHakiController extends Controller
{
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $riwayat = PegawaiHaki::query()
            ->where('id_pegawai', (int) $pegawai->id)
            ->orderByDesc('id')
            ->get();

        foreach ($riwayat as $row) {
            $row->sertifikat_url = $this->resolveDocumentUrl($row->sertifikat);
        }

        return view('pegawai.riwayat_haki.index', [
            'CurrentPage' => 'content',
            'title'       => 'Riwayat HaKI',
            'riwayat'     => $riwayat,
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

        if ($request->hasFile('sertifikat')) {
            $validated['sertifikat'] = $request->file('sertifikat')->store('haki', 'public');
        }

        PegawaiHaki::create($validated);

        return redirect()->route('pegawai.riwayat-haki.index')->with('status', 'Riwayat HaKI berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiHaki::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $validated = $this->validateData($request);

        if ($request->hasFile('sertifikat')) {
            $this->deleteStoredFileIfExists($row->sertifikat);
            $validated['sertifikat'] = $request->file('sertifikat')->store('haki', 'public');
        } else {
            unset($validated['sertifikat']);
        }

        $row->update($validated);

        return redirect()->route('pegawai.riwayat-haki.index')->with('status', 'Riwayat HaKI berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiHaki::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $this->deleteStoredFileIfExists($row->sertifikat);

        $row->delete();

        return redirect()->route('pegawai.riwayat-haki.index')->with('status', 'Riwayat HaKI berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'judul'        => 'required|string|max:255',
            'pemilik'      => 'nullable|string|max:255',
            'tahun_ajaran' => 'required|digits:4',
            'sertifikat'   => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
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
            'assets/haki/' . ltrim($value, '/'),
            'uploads/haki/' . ltrim($value, '/'),
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

        if (Storage::disk('public')->exists('haki/' . ltrim($value, '/'))) {
            return asset('storage/haki/' . ltrim($value, '/'));
        }

        return null;
    }
}
