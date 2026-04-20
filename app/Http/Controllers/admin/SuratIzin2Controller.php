<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\RefKategoriSurat;
use App\Models\SuratIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SuratIzin2Controller extends Controller
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

        $data['title'] = 'Surat Izin Tidak Masuk';
        $data['CurrentPage'] = 'content';
        $data['tanggal_awal'] = $from;
        $data['tanggal_akhir'] = $to;
        $data['suratList'] = SuratIzin::query()
            ->from('surat_izin as si')
            ->leftJoin('pegawai as p', 'p.id', '=', 'si.id_dosen')
            ->leftJoin('ref_kategori_surat as rks', 'rks.id', '=', 'si.id_kategori')
            ->select('si.*', 'p.nama as nama_pengirim', 'rks.nama as kategori_nama')
            ->whereBetween('si.tgl_surat', [$from, $to])
            ->orderBy('si.tgl_surat', 'desc')
            ->orderBy('si.id', 'desc')
            ->get();

        return view('admin.surat_izin2.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Surat Izin';
        $data['CurrentPage'] = 'content';
        $data['pegawaiList'] = Pegawai::orderBy('nama')->get(['id', 'npp', 'nama']);
        $data['kategoriList'] = RefKategoriSurat::orderBy('nama')->get(['id', 'nama']);

        return view('admin.surat_izin2.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request, true);

        $fileName = $this->storeFile($request);

        SuratIzin::create([
            'id_dosen' => (int) $validated['id_dosen'],
            'tgl_surat' => $validated['tgl_surat'],
            'perihal' => trim($validated['perihal']),
            'keterangan' => trim($validated['keterangan']),
            'file_surat' => $fileName,
            'dilihat' => (int) ($validated['dilihat'] ?? 0),
            'izin_mgr_sdm' => (int) $validated['izin_mgr_sdm'],
            'izin_ka_jenjang' => (int) $validated['izin_ka_jenjang'],
            'id_kategori' => isset($validated['id_kategori']) ? (int) $validated['id_kategori'] : null,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari' => $this->calculateDays($validated['tanggal_mulai'], $validated['tanggal_selesai']),
        ]);

        return redirect('simpeg/SuratIzin2')->with('status', 'Data surat izin berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $surat = SuratIzin::find($id);
        if (!$surat) {
            return redirect('simpeg/SuratIzin2')->with('error', 'Data surat izin tidak ditemukan.');
        }

        $data['title'] = 'Edit Surat Izin';
        $data['CurrentPage'] = 'content';
        $data['d'] = $surat;
        $data['pegawaiList'] = Pegawai::orderBy('nama')->get(['id', 'npp', 'nama']);
        $data['kategoriList'] = RefKategoriSurat::orderBy('nama')->get(['id', 'nama']);

        return view('admin.surat_izin2.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $surat = SuratIzin::find($id);
        if (!$surat) {
            return redirect('simpeg/SuratIzin2')->with('error', 'Data surat izin tidak ditemukan.');
        }

        $validated = $this->validateRequest($request, false);

        $fileName = $surat->file_surat;
        if ($request->hasFile('file_surat')) {
            $newFileName = $this->storeFile($request);
            if (!empty($surat->file_surat)) {
                $oldPath = public_path('assets/surat_izin/' . $surat->file_surat);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            $fileName = $newFileName;
        }

        $surat->update([
            'id_dosen' => (int) $validated['id_dosen'],
            'tgl_surat' => $validated['tgl_surat'],
            'perihal' => trim($validated['perihal']),
            'keterangan' => trim($validated['keterangan']),
            'file_surat' => $fileName,
            'dilihat' => (int) ($validated['dilihat'] ?? 0),
            'izin_mgr_sdm' => (int) $validated['izin_mgr_sdm'],
            'izin_ka_jenjang' => (int) $validated['izin_ka_jenjang'],
            'id_kategori' => isset($validated['id_kategori']) ? (int) $validated['id_kategori'] : null,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'jumlah_hari' => $this->calculateDays($validated['tanggal_mulai'], $validated['tanggal_selesai']),
        ]);

        return redirect('simpeg/SuratIzin2')->with('status', 'Data surat izin berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $surat = SuratIzin::find($id);
        if (!$surat) {
            return redirect('simpeg/SuratIzin2')->with('error', 'Data surat izin tidak ditemukan.');
        }

        if (!empty($surat->file_surat)) {
            $filePath = public_path('assets/surat_izin/' . $surat->file_surat);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $surat->delete();

        return redirect('simpeg/SuratIzin2')->with('status', 'Data surat izin berhasil dihapus.');
    }

    private function validateRequest(Request $request, bool $isCreate): array
    {
        $rules = [
            'id_dosen' => 'required|integer|exists:pegawai,id',
            'tgl_surat' => 'required|date',
            'perihal' => 'required|string|max:120',
            'keterangan' => 'required|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'id_kategori' => 'nullable|integer|exists:ref_kategori_surat,id',
            'izin_mgr_sdm' => 'required|in:0,1',
            'izin_ka_jenjang' => 'required|in:0,1',
            'dilihat' => 'required|in:0,1',
            'file_surat' => ($isCreate ? 'required' : 'nullable') . '|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:4096',
        ];

        return $request->validate($rules);
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
        $fileName = 'surat_izin_' . date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $extension;
        $file->move($dir, $fileName);

        return $fileName;
    }
}
