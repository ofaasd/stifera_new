<?php

namespace App\Http\Controllers;

use App\Models\PegawaiBkd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PegawaiRiwayatBkdController extends Controller
{
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $riwayat = PegawaiBkd::query()
            ->where('id_pegawai', (int) $pegawai->id)
            ->orderByDesc('id')
            ->get();

        foreach ($riwayat as $row) {
            $row->lampiran_url = $this->resolveLampiranUrl($row->lampiran);
        }

        return view('pegawai.riwayat_bkd.index', [
            'CurrentPage' => 'content',
            'title' => 'Riwayat BKD',
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

        if ($request->hasFile('lampiran')) {
            $validated['lampiran'] = $request->file('lampiran')->store('bkd', 'public');
        }

        PegawaiBkd::create($validated);

        return redirect()->route('pegawai.riwayat-bkd.index')->with('status', 'Riwayat BKD berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiBkd::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $validated = $this->validateData($request);

        if ($request->hasFile('lampiran')) {
            if (!empty($row->lampiran) && Storage::disk('public')->exists($row->lampiran)) {
                Storage::disk('public')->delete($row->lampiran);
            }
            $validated['lampiran'] = $request->file('lampiran')->store('bkd', 'public');
        } else {
            unset($validated['lampiran']);
        }

        $row->update($validated);

        return redirect()->route('pegawai.riwayat-bkd.index')->with('status', 'Riwayat BKD berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiBkd::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        if (!empty($row->lampiran) && Storage::disk('public')->exists($row->lampiran)) {
            Storage::disk('public')->delete($row->lampiran);
        }

        $row->delete();

        return redirect()->route('pegawai.riwayat-bkd.index')->with('status', 'Riwayat BKD berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'periode_bkd' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'jabatan_fungsional' => 'nullable|string|max:100',
            'assesor1' => 'nullable|string|max:100',
            'status_validasi1' => 'nullable|integer|min:0|max:1',
            'assesor2' => 'nullable|string|max:100',
            'status_validasi2' => 'nullable|integer|min:0|max:1',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);
    }

    private function resolveLampiranUrl(?string $lampiran): ?string
    {
        if (empty($lampiran)) {
            return null;
        }

        $lampiran = str_replace('\\', '/', trim($lampiran));

        if (preg_match('/^https?:\/\//i', $lampiran) === 1) {
            return $lampiran;
        }

        $publicCandidates = [
            $lampiran,
            'assets/bkd/' . ltrim($lampiran, '/'),
            'assets/lampiran_bkd/' . ltrim($lampiran, '/'),
            'uploads/bkd/' . ltrim($lampiran, '/'),
        ];

        foreach ($publicCandidates as $candidate) {
            $candidate = trim($candidate, '/');
            if ($candidate !== '' && is_file(public_path($candidate))) {
                return asset($candidate);
            }
        }

        if (Storage::disk('public')->exists($lampiran)) {
            return asset('storage/' . ltrim($lampiran, '/'));
        }

        return null;
    }
}
