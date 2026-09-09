<?php

namespace App\Http\Controllers;

use App\Models\IzinMeninggalkanPekerjaan;
use App\Models\RefKategoriSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PegawaiMeninggalkanPekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $from = $request->input('tanggal_awal', now()->subMonths(3)->toDateString());
        $to = $request->input('tanggal_akhir', now()->toDateString());

        if (strtotime($to) < strtotime($from)) {
            $tmp = $from;
            $from = $to;
            $to = $tmp;
        }

        $query = IzinMeninggalkanPekerjaan::query()
            ->from('izin_meninggalkan_pekerjaan as imp')
            ->leftJoin('ref_kategori_surat as rks', 'rks.id', '=', 'imp.id_kategori')
            ->leftJoin('pegawai as p', 'p.id', '=', 'imp.id_dosen')
            ->select('imp.*', 'rks.nama as kategori_nama', 'p.nama as nama_pengirim')
            ->whereBetween('imp.tanggal', [$from, $to])
            ->orderBy('imp.tanggal', 'desc')
            ->orderBy('imp.id', 'desc');

        $s2 = \Illuminate\Support\Facades\DB::table('struktur_pegawai2')->first();
        $isPimpinan = false;
        if ($s2 && in_array($pegawai->npp, [$s2->ketua_st, $s2->pembantu_1, $s2->pembantu_2, $s2->pembantu_3])) {
            $isPimpinan = true;
        }

        if (!$isPimpinan) {
            $query->where('imp.id_dosen', (int) $pegawai->id);
        }

        return view('pegawai.meninggalkan_pekerjaan.index', [
            'title' => 'Surat Izin Meninggalkan Pekerjaan',
            'CurrentPage' => 'content',
            'tanggal_awal' => $from,
            'tanggal_akhir' => $to,
            'isPimpinan' => $isPimpinan,
            'izinList' => $query->get(),
        ]);
    }

    public function create()
    {
        return view('pegawai.meninggalkan_pekerjaan.create', [
            'title' => 'Tambah Izin Meninggalkan Pekerjaan',
            'CurrentPage' => 'content',
            'kategoriList' => RefKategoriSurat::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function store(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $validated = $this->validateRequest($request, true);

        IzinMeninggalkanPekerjaan::create([
            'id_dosen' => (int) $pegawai->id,
            'tanggal' => $validated['tanggal'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'waktu_mulai' => $validated['waktu_mulai'] . ':00',
            'waktu_selesai' => $validated['waktu_selesai'] . ':00',
            'keperluan' => trim($validated['keperluan']),
            'izin_ka_jenjang' => 0,
            'izin_mgr_sdm' => 0,
            'id_kategori' => isset($validated['id_kategori']) ? (int) $validated['id_kategori'] : null,
            'lampiran' => $this->storeAttachment($request),
            'jumlah_hari' => $this->calculateDays($validated['tanggal'], $validated['tanggal_selesai']),
        ]);

        return redirect('pegawai/MeninggalkanPekerjaan')->with('status', 'Izin meninggalkan pekerjaan berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $izin = IzinMeninggalkanPekerjaan::where('id', (int) $id)
            ->where('id_dosen', (int) $pegawai->id)
            ->first();

        if (!$izin) {
            return redirect('pegawai/MeninggalkanPekerjaan')->with('error', 'Data izin meninggalkan pekerjaan tidak ditemukan.');
        }

        return view('pegawai.meninggalkan_pekerjaan.edit', [
            'title' => 'Edit Izin Meninggalkan Pekerjaan',
            'CurrentPage' => 'content',
            'd' => $izin,
            'kategoriList' => RefKategoriSurat::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $izin = IzinMeninggalkanPekerjaan::where('id', (int) $id)
            ->where('id_dosen', (int) $pegawai->id)
            ->first();

        if (!$izin) {
            return redirect('pegawai/MeninggalkanPekerjaan')->with('error', 'Data izin meninggalkan pekerjaan tidak ditemukan.');
        }

        $validated = $this->validateRequest($request, false);

        $lampiran = $izin->lampiran;
        if ($request->hasFile('lampiran')) {
            $lampiran = $this->storeAttachment($request);
            if (!empty($izin->lampiran)) {
                $oldPath = public_path('assets/izin_meninggalkan_pekerjaan/' . $izin->lampiran);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
        }

        $izin->update([
            'tanggal' => $validated['tanggal'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'waktu_mulai' => $validated['waktu_mulai'] . ':00',
            'waktu_selesai' => $validated['waktu_selesai'] . ':00',
            'keperluan' => trim($validated['keperluan']),
            'id_kategori' => isset($validated['id_kategori']) ? (int) $validated['id_kategori'] : null,
            'lampiran' => $lampiran,
            'jumlah_hari' => $this->calculateDays($validated['tanggal'], $validated['tanggal_selesai']),
            'izin_ka_jenjang' => 0,
            'izin_mgr_sdm' => 0,
        ]);

        return redirect('pegawai/MeninggalkanPekerjaan')->with('status', 'Izin meninggalkan pekerjaan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $izin = IzinMeninggalkanPekerjaan::where('id', (int) $id)
            ->where('id_dosen', (int) $pegawai->id)
            ->first();

        if (!$izin) {
            return redirect('pegawai/MeninggalkanPekerjaan')->with('error', 'Data izin meninggalkan pekerjaan tidak ditemukan.');
        }

        if (!empty($izin->lampiran)) {
            $filePath = public_path('assets/izin_meninggalkan_pekerjaan/' . $izin->lampiran);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $izin->delete();

        return redirect('pegawai/MeninggalkanPekerjaan')->with('status', 'Izin meninggalkan pekerjaan berhasil dihapus.');
    }

    public function toggleValidasi(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        $s2 = \Illuminate\Support\Facades\DB::table('struktur_pegawai2')->first();
        if (!$s2 || !in_array($pegawai->npp, [$s2->ketua_st, $s2->pembantu_1, $s2->pembantu_2, $s2->pembantu_3])) {
            abort(403);
        }

        $izin = IzinMeninggalkanPekerjaan::findOrFail($id);
        $izin->update(['izin_ka_jenjang' => $izin->izin_ka_jenjang == 1 ? 0 : 1]);

        return back()->with('status', 'Status validasi berhasil diubah.');
    }

    private function validateRequest(Request $request, bool $isCreate): array
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
            'keperluan' => 'required|string',
            'id_kategori' => 'nullable|integer|exists:ref_kategori_surat,id',
            'lampiran' => ($isCreate ? 'required' : 'nullable') . '|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:4096',
        ]);

        if (strtotime($validated['waktu_selesai']) <= strtotime($validated['waktu_mulai'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'waktu_selesai' => 'Waktu selesai harus lebih besar dari waktu mulai.',
            ]);
        }

        return $validated;
    }

    private function calculateDays(string $startDate, string $endDate): int
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);

        return (int) floor(($end - $start) / 86400) + 1;
    }

    private function storeAttachment(Request $request): string
    {
        $file = $request->file('lampiran');
        $dir = public_path('assets/izin_meninggalkan_pekerjaan');
        File::ensureDirectoryExists($dir);

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $fileName = 'imp_pegawai_' . date('YmdHis') . '_' . mt_rand(100, 999) . '.' . $extension;
        $file->move($dir, $fileName);

        return $fileName;
    }
}
