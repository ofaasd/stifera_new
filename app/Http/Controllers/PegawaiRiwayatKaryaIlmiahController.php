<?php

namespace App\Http\Controllers;

use App\Models\PegawaiKaryaIlmiah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PegawaiRiwayatKaryaIlmiahController extends Controller
{
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $riwayat = PegawaiKaryaIlmiah::query()
            ->where('id_pegawai', (int) $pegawai->id)
            ->orderByDesc('id')
            ->get();

        foreach ($riwayat as $row) {
            $row->file_url = $this->resolveDocumentUrl($row->file);
        }

        return view('pegawai.riwayat_karya_ilmiah.index', [
            'CurrentPage' => 'content',
            'title' => 'Riwayat Karya Ilmiah',
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
        $validated['tahun'] = $validated['tahun'] . '-01-01';

        if ($request->hasFile('file')) {
            $validated['file'] = $request->file('file')->store('karya-ilmiah', 'public');
        }

        PegawaiKaryaIlmiah::create($validated);

        return redirect()->route('pegawai.riwayat-karya-ilmiah.index')->with('status', 'Riwayat karya ilmiah berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiKaryaIlmiah::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $validated = $this->validateData($request);
        $validated['tahun'] = $validated['tahun'] . '-01-01';

        if ($request->hasFile('file')) {
            $this->deleteStoredFileIfExists($row->file);
            $validated['file'] = $request->file('file')->store('karya-ilmiah', 'public');
        } else {
            unset($validated['file']);
        }

        $row->update($validated);

        return redirect()->route('pegawai.riwayat-karya-ilmiah.index')->with('status', 'Riwayat karya ilmiah berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiKaryaIlmiah::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $this->deleteStoredFileIfExists($row->file);

        $row->delete();

        return redirect()->route('pegawai.riwayat-karya-ilmiah.index')->with('status', 'Riwayat karya ilmiah berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'judul' => 'required|string|max:255',
            'nama_majalah' => 'required|string|max:255',
            'volume' => 'required|integer|min:0',
            'nomor' => 'required|integer|min:0',
            'bulan' => 'required|string|max:50',
            'tahun' => 'required|digits:4',
            'link_url' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
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
            'assets/karya-ilmiah/' . ltrim($value, '/'),
            'uploads/karya-ilmiah/' . ltrim($value, '/'),
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

        $storageCandidates = [
            'karya-ilmiah/' . ltrim($value, '/'),
        ];

        foreach ($storageCandidates as $candidate) {
            if (Storage::disk('public')->exists($candidate)) {
                return asset('storage/' . ltrim($candidate, '/'));
            }
        }

        return null;
    }
}
