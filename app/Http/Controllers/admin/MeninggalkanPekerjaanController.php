<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\IzinMeninggalkanPekerjaan;
use App\Models\Pegawai;
use App\Models\RefKategoriSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MeninggalkanPekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('tanggal_awal', now()->startOfMonth()->toDateString());
        $to = $request->input('tanggal_akhir', now()->toDateString());

        if (strtotime($to) < strtotime($from)) {
            $tmp = $from;
            $from = $to;
            $to = $tmp;
        }

        return view('admin.meninggalkan_pekerjaan.index', [
            'title' => 'Surat Izin Meninggalkan Pekerjaan',
            'CurrentPage' => 'content',
            'tanggal_awal' => $from,
            'tanggal_akhir' => $to,
            'izinList' => IzinMeninggalkanPekerjaan::query()
                ->from('izin_meninggalkan_pekerjaan as imp')
                ->leftJoin('pegawai as p', 'p.id', '=', 'imp.id_dosen')
                ->leftJoin('ref_kategori_surat as rks', 'rks.id', '=', 'imp.id_kategori')
                ->select('imp.*', 'p.nama as nama_pengirim', 'rks.nama as kategori_nama')
                ->whereBetween('imp.tanggal', [$from, $to])
                ->orderBy('imp.tanggal', 'desc')
                ->orderBy('imp.id', 'desc')
                ->get(),
        ]);
    }

    public function create()
    {
        return view('admin.meninggalkan_pekerjaan.create', [
            'title' => 'Tambah Izin Meninggalkan Pekerjaan',
            'CurrentPage' => 'content',
            'pegawaiList' => Pegawai::orderBy('nama')->get(['id', 'npp', 'nama']),
            'kategoriList' => RefKategoriSurat::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request, true);

        IzinMeninggalkanPekerjaan::create([
            'id_dosen' => (int) $validated['id_dosen'],
            'tanggal' => $validated['tanggal'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'waktu_mulai' => $validated['waktu_mulai'] . ':00',
            'waktu_selesai' => $validated['waktu_selesai'] . ':00',
            'keperluan' => trim($validated['keperluan']),
            'izin_ka_jenjang' => (int) $validated['izin_ka_jenjang'],
            'izin_mgr_sdm' => (int) $validated['izin_mgr_sdm'],
            'id_kategori' => isset($validated['id_kategori']) ? (int) $validated['id_kategori'] : null,
            'lampiran' => $this->storeAttachment($request),
            'jumlah_hari' => $this->calculateDays($validated['tanggal'], $validated['tanggal_selesai']),
        ]);

        return redirect('simpeg/MeninggalkanPekerjaan')->with('status', 'Data izin meninggalkan pekerjaan berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $izin = IzinMeninggalkanPekerjaan::find($id);
        if (!$izin) {
            return redirect('simpeg/MeninggalkanPekerjaan')->with('error', 'Data izin meninggalkan pekerjaan tidak ditemukan.');
        }

        return view('admin.meninggalkan_pekerjaan.edit', [
            'title' => 'Edit Izin Meninggalkan Pekerjaan',
            'CurrentPage' => 'content',
            'd' => $izin,
            'pegawaiList' => Pegawai::orderBy('nama')->get(['id', 'npp', 'nama']),
            'kategoriList' => RefKategoriSurat::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $izin = IzinMeninggalkanPekerjaan::find($id);
        if (!$izin) {
            return redirect('simpeg/MeninggalkanPekerjaan')->with('error', 'Data izin meninggalkan pekerjaan tidak ditemukan.');
        }

        $validated = $this->validateRequest($request, false);

        $lampiran = $izin->lampiran;
        if ($request->hasFile('lampiran')) {
            $newLampiran = $this->storeAttachment($request);
            if (!empty($izin->lampiran)) {
                $oldPath = public_path('assets/izin_meninggalkan_pekerjaan/' . $izin->lampiran);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            $lampiran = $newLampiran;
        }

        $izin->update([
            'id_dosen' => (int) $validated['id_dosen'],
            'tanggal' => $validated['tanggal'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'waktu_mulai' => $validated['waktu_mulai'] . ':00',
            'waktu_selesai' => $validated['waktu_selesai'] . ':00',
            'keperluan' => trim($validated['keperluan']),
            'izin_ka_jenjang' => (int) $validated['izin_ka_jenjang'],
            'izin_mgr_sdm' => (int) $validated['izin_mgr_sdm'],
            'id_kategori' => isset($validated['id_kategori']) ? (int) $validated['id_kategori'] : null,
            'lampiran' => $lampiran,
            'jumlah_hari' => $this->calculateDays($validated['tanggal'], $validated['tanggal_selesai']),
        ]);

        return redirect('simpeg/MeninggalkanPekerjaan')->with('status', 'Data izin meninggalkan pekerjaan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $izin = IzinMeninggalkanPekerjaan::find($id);
        if (!$izin) {
            return redirect('simpeg/MeninggalkanPekerjaan')->with('error', 'Data izin meninggalkan pekerjaan tidak ditemukan.');
        }

        if (!empty($izin->lampiran)) {
            $filePath = public_path('assets/izin_meninggalkan_pekerjaan/' . $izin->lampiran);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $izin->delete();

        return redirect('simpeg/MeninggalkanPekerjaan')->with('status', 'Data izin meninggalkan pekerjaan berhasil dihapus.');
    }

    private function validateRequest(Request $request, bool $isCreate): array
    {
        $validated = $request->validate([
            'id_dosen' => 'required|integer|exists:pegawai,id',
            'tanggal' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
            'keperluan' => 'required|string',
            'izin_ka_jenjang' => 'required|in:0,1',
            'izin_mgr_sdm' => 'required|in:0,1',
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
        $fileName = 'imp_' . date('YmdHis') . '_' . mt_rand(100, 999) . '.' . $extension;
        $file->move($dir, $fileName);

        return $fileName;
    }
}
