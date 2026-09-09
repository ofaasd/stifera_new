<?php

namespace App\Http\Controllers;

use App\Models\RefKategoriSurat;
use App\Models\SuratIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PegawaiSuratIzinController extends Controller
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

        $query = SuratIzin::query()
            ->from('surat_izin as si')
            ->leftJoin('ref_kategori_surat as rks', 'rks.id', '=', 'si.id_kategori')
            ->leftJoin('pegawai as p', 'p.id', '=', 'si.id_dosen')
            ->select('si.*', 'rks.nama as kategori_nama', 'p.nama as nama_pengirim')
            ->whereBetween('si.tgl_surat', [$from, $to])
            ->orderBy('si.tgl_surat', 'desc')
            ->orderBy('si.id', 'desc');

        $s2 = \Illuminate\Support\Facades\DB::table('struktur_pegawai2')->first();
        $isPimpinan = false;
        if ($s2 && in_array($pegawai->npp, [$s2->ketua_st, $s2->pembantu_1, $s2->pembantu_2, $s2->pembantu_3])) {
            $isPimpinan = true;
        }

        if (!$isPimpinan) {
            $query->where('si.id_dosen', (int) $pegawai->id);
        }

        return view('pegawai.surat_izin.index', [
            'title' => 'Surat Izin Tidak Masuk',
            'CurrentPage' => 'content',
            'tanggal_awal' => $from,
            'tanggal_akhir' => $to,
            'isPimpinan' => $isPimpinan,
            'suratList' => $query->get(),
        ]);
    }

    public function create()
    {
        return view('pegawai.surat_izin.create', [
            'title' => 'Tambah Surat Izin',
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

        SuratIzin::create([
            'id_dosen' => (int) $pegawai->id,
            'tgl_surat' => $validated['tgl_surat'],
            'perihal' => trim($validated['perihal']),
            'keterangan' => trim($validated['keterangan']),
            'file_surat' => $this->storeFile($request),
            'dilihat' => 0,
            'izin_mgr_sdm' => 0,
            'izin_ka_jenjang' => 0,
            'id_kategori' => isset($validated['id_kategori']) ? (int) $validated['id_kategori'] : null,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari' => $this->calculateDays($validated['tanggal_mulai'], $validated['tanggal_selesai']),
        ]);

        return redirect('pegawai/SuratIzin/index2')->with('status', 'Surat izin berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $surat = SuratIzin::where('id', (int) $id)
            ->where('id_dosen', (int) $pegawai->id)
            ->first();

        if (!$surat) {
            return redirect('pegawai/SuratIzin/index2')->with('error', 'Data surat izin tidak ditemukan.');
        }

        return view('pegawai.surat_izin.edit', [
            'title' => 'Edit Surat Izin',
            'CurrentPage' => 'content',
            'd' => $surat,
            'kategoriList' => RefKategoriSurat::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $surat = SuratIzin::where('id', (int) $id)
            ->where('id_dosen', (int) $pegawai->id)
            ->first();

        if (!$surat) {
            return redirect('pegawai/SuratIzin/index2')->with('error', 'Data surat izin tidak ditemukan.');
        }

        $validated = $this->validateRequest($request, false);

        $fileName = $surat->file_surat;
        if ($request->hasFile('file_surat')) {
            $fileName = $this->storeFile($request);
            if (!empty($surat->file_surat)) {
                $oldPath = public_path('assets/surat_izin/' . $surat->file_surat);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
        }

        $surat->update([
            'tgl_surat' => $validated['tgl_surat'],
            'perihal' => trim($validated['perihal']),
            'keterangan' => trim($validated['keterangan']),
            'file_surat' => $fileName,
            'id_kategori' => isset($validated['id_kategori']) ? (int) $validated['id_kategori'] : null,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari' => $this->calculateDays($validated['tanggal_mulai'], $validated['tanggal_selesai']),
            // Approval status kembali menunggu saat pegawai ubah surat
            'izin_mgr_sdm' => 0,
            'izin_ka_jenjang' => 0,
            'dilihat' => 0,
        ]);

        return redirect('pegawai/SuratIzin/index2')->with('status', 'Surat izin berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $surat = SuratIzin::where('id', (int) $id)
            ->where('id_dosen', (int) $pegawai->id)
            ->first();

        if (!$surat) {
            return redirect('pegawai/SuratIzin/index2')->with('error', 'Data surat izin tidak ditemukan.');
        }

        if (!empty($surat->file_surat)) {
            $filePath = public_path('assets/surat_izin/' . $surat->file_surat);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $surat->delete();

        return redirect('pegawai/SuratIzin/index2')->with('status', 'Surat izin berhasil dihapus.');
    }

    public function toggleValidasi(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        $s2 = \Illuminate\Support\Facades\DB::table('struktur_pegawai2')->first();
        if (!$s2 || !in_array($pegawai->npp, [$s2->ketua_st, $s2->pembantu_1, $s2->pembantu_2, $s2->pembantu_3])) {
            abort(403);
        }

        $surat = SuratIzin::findOrFail($id);
        $surat->update(['izin_ka_jenjang' => $surat->izin_ka_jenjang == 1 ? 0 : 1]);

        return back()->with('status', 'Status validasi berhasil diubah.');
    }

    private function validateRequest(Request $request, bool $isCreate): array
    {
        return $request->validate([
            'tgl_surat' => 'required|date',
            'perihal' => 'required|string|max:120',
            'keterangan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'id_kategori' => 'nullable|integer|exists:ref_kategori_surat,id',
            'file_surat' => ($isCreate ? 'required' : 'nullable') . '|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:4096',
        ]);
    }

    private function calculateDays(string $startDate, string $endDate): int
    {
        $start = strtotime($startDate);
        $end = strtotime($endDate);

        return (int) floor(($end - $start) / 86400) + 1;
    }

    private function storeFile(Request $request): string
    {
        $file = $request->file('file_surat');
        $dir = public_path('assets/surat_izin');
        File::ensureDirectoryExists($dir);

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $fileName = 'surat_izin_pegawai_' . date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $extension;
        $file->move($dir, $fileName);

        return $fileName;
    }
}
