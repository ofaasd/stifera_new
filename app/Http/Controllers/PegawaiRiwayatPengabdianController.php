<?php

namespace App\Http\Controllers;

use App\Models\PegawaiPengabdian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PegawaiRiwayatPengabdianController extends Controller
{
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $riwayat = PegawaiPengabdian::query()
            ->where('id_pegawai', (int) $pegawai->id)
            ->orderByDesc('id')
            ->get();

        foreach ($riwayat as $row) {
            $row->bukti_url = $this->resolveDocumentUrl($row->bukti, 'bukti');
            $row->proposal_url = $this->resolveDocumentUrl($row->proposal, 'proposal');
        }

        return view('pegawai.riwayat_pengabdian.index', [
            'CurrentPage' => 'content',
            'title' => 'Riwayat Pengabdian',
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

        if ($request->hasFile('bukti')) {
            $validated['bukti'] = $request->file('bukti')->store('pengabdian/bukti', 'public');
        }

        if ($request->hasFile('proposal')) {
            $validated['proposal'] = $request->file('proposal')->store('pengabdian/proposal', 'public');
        }

        PegawaiPengabdian::create($validated);

        return redirect()->route('pegawai.riwayat-pengabdian.index')->with('status', 'Riwayat pengabdian berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiPengabdian::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $validated = $this->validateData($request);
        $validated['tahun'] = $validated['tahun'] . '-01-01';

        if ($request->hasFile('bukti')) {
            $this->deleteStoredFileIfExists($row->bukti);
            $validated['bukti'] = $request->file('bukti')->store('pengabdian/bukti', 'public');
        } else {
            unset($validated['bukti']);
        }

        if ($request->hasFile('proposal')) {
            $this->deleteStoredFileIfExists($row->proposal);
            $validated['proposal'] = $request->file('proposal')->store('pengabdian/proposal', 'public');
        } else {
            unset($validated['proposal']);
        }

        $row->update($validated);

        return redirect()->route('pegawai.riwayat-pengabdian.index')->with('status', 'Riwayat pengabdian berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiPengabdian::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $this->deleteStoredFileIfExists($row->bukti);
        $this->deleteStoredFileIfExists($row->proposal);

        $row->delete();

        return redirect()->route('pegawai.riwayat-pengabdian.index')->with('status', 'Riwayat pengabdian berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tahun' => 'required|digits:4',
            'tempat' => 'nullable|string|max:255',
            'link_url' => 'nullable|string|max:255',
            'bukti' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'ketua' => 'required|integer|in:0,1',
            'proposal' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);
    }

    private function deleteStoredFileIfExists(?string $path): void
    {
        if (!empty($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
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

        $folderType = $type === 'proposal' ? 'proposal' : 'bukti';
        $publicCandidates = [
            $value,
            'assets/pengabdian/' . ltrim($value, '/'),
            'assets/pengabdian/' . $folderType . '/' . ltrim($value, '/'),
            'uploads/pengabdian/' . ltrim($value, '/'),
            'uploads/pengabdian/' . $folderType . '/' . ltrim($value, '/'),
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
            'pengabdian/' . ltrim($value, '/'),
            'pengabdian/' . $folderType . '/' . ltrim($value, '/'),
        ];

        foreach ($storageCandidates as $candidate) {
            if (Storage::disk('public')->exists($candidate)) {
                return asset('storage/' . ltrim($candidate, '/'));
            }
        }

        return null;
    }
}
