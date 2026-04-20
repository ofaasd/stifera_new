<?php

namespace App\Http\Controllers;

use App\Models\PegawaiPenelitian;
use App\Models\PegawaiAnggotaPenelitian;
use App\Models\Pegawai;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PegawaiRiwayatPenelitianController extends Controller
{
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $riwayat = PegawaiPenelitian::query()
            ->where('id_pegawai', (int) $pegawai->id)
            ->with(['ketua', 'anggota', 'anggota.pegawai', 'anggota.mahasiswa'])
            ->orderByDesc('id')
            ->get();

        foreach ($riwayat as $row) {
            $row->dokumen_url = $this->resolveDocumentUrl($row->dokumen, 'dokumen');
            $row->proposal_url = $this->resolveDocumentUrl($row->proposal, 'proposal');
            $row->lap_kemajuan_url = $this->resolveDocumentUrl($row->lap_kemajuan, 'lap_kemajuan');
            $row->lap_keuangan_url = $this->resolveDocumentUrl($row->lap_keuangan, 'lap_keuangan');
            $row->lap_akhir_url = $this->resolveDocumentUrl($row->lap_akhir, 'lap_akhir');
        }

        $pegawaiList = Pegawai::orderBy('nama')->get(['id', 'nama', 'npp']);
        $mahasiswaList = Mahasiswa::orderBy('nama')->get(['id', 'nama', 'nim']);
        $fakultasList = DB::table('fakultas')->orderBy('nama_fakultas')->get(['id', 'nama_fakultas']);

        return view('pegawai.riwayat_penelitian.index', [
            'CurrentPage' => 'content',
            'title' => 'Riwayat Penelitian',
            'riwayat' => $riwayat,
            'pegawaiList' => $pegawaiList,
            'mahasiswaList' => $mahasiswaList,
            'fakultasList' => $fakultasList,
        ]);
    }

    public function store(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $validated = $request->validate([
            'nomor' => 'nullable|string|max:255',
            'judul' => 'required|string|max:255',
            'id_fakultas' => 'required|integer|exists:fakultas,id',
            'jenis_penelitian' => 'nullable|string|max:255',
            'tahun' => 'required|digits:4',
            'sumber_dana' => 'nullable|string|max:255',
            'dana' => 'nullable|integer|min:0',
            'no_surat' => 'nullable|string|max:255',
            'penyelenggara' => 'nullable|string|max:255',
            'id_ketua' => 'nullable|integer|exists:pegawai,id',
            'anggota_pegawai' => 'nullable|array',
            'anggota_pegawai.*' => 'integer|exists:pegawai,id',
            'anggota_mahasiswa' => 'nullable|array',
            'anggota_mahasiswa.*' => 'integer|exists:mahasiswa,id',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'proposal' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'lap_kemajuan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'lap_keuangan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'lap_akhir' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $this->storeUploadedFiles($request, $validated);

        $validated['id_pegawai'] = (int) $pegawai->id;
        $validated['tahun'] = $validated['tahun'] . '-01-01';
        
        $anggotaPegawai = $validated['anggota_pegawai'] ?? [];
        $anggotaMahasiswa = $validated['anggota_mahasiswa'] ?? [];
        
        unset($validated['anggota_pegawai'], $validated['anggota_mahasiswa']);

        $penelitian = PegawaiPenelitian::create($validated);
        
        $this->storeAnggota($penelitian->id, $anggotaPegawai, $anggotaMahasiswa);

        return redirect()->route('pegawai.riwayat-penelitian.index')->with('status', 'Riwayat penelitian berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiPenelitian::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $validated = $request->validate([
            'nomor' => 'nullable|string|max:255',
            'judul' => 'required|string|max:255',
            'id_fakultas' => 'required|integer|exists:fakultas,id',
            'jenis_penelitian' => 'nullable|string|max:255',
            'tahun' => 'required|digits:4',
            'sumber_dana' => 'nullable|string|max:255',
            'dana' => 'nullable|integer|min:0',
            'no_surat' => 'nullable|string|max:255',
            'penyelenggara' => 'nullable|string|max:255',
                'id_ketua' => 'nullable|integer|exists:pegawai,id',
                'anggota_pegawai' => 'nullable|array',
                'anggota_pegawai.*' => 'integer|exists:pegawai,id',
                'anggota_mahasiswa' => 'nullable|array',
                'anggota_mahasiswa.*' => 'integer|exists:mahasiswa,id',
            'dokumen' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'proposal' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'lap_kemajuan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'lap_keuangan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'lap_akhir' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        $this->storeUploadedFiles($request, $validated, $row);

        $validated['tahun'] = $validated['tahun'] . '-01-01';


            $anggotaPegawai = $validated['anggota_pegawai'] ?? [];
            $anggotaMahasiswa = $validated['anggota_mahasiswa'] ?? [];
        
            unset($validated['anggota_pegawai'], $validated['anggota_mahasiswa']);

            $row->update($validated);

            $this->storeAnggota($row->id, $anggotaPegawai, $anggotaMahasiswa);

            return redirect()->route('pegawai.riwayat-penelitian.index')->with('status', 'Riwayat penelitian berhasil diperbarui.');

            }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiPenelitian::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $this->deleteStoredDocumentIfExists($row->dokumen, 'dokumen');
        $this->deleteStoredDocumentIfExists($row->proposal, 'proposal');
        $this->deleteStoredDocumentIfExists($row->lap_kemajuan, 'lap_kemajuan');
        $this->deleteStoredDocumentIfExists($row->lap_keuangan, 'lap_keuangan');
        $this->deleteStoredDocumentIfExists($row->lap_akhir, 'lap_akhir');

        $row->delete();

        return redirect()->route('pegawai.riwayat-penelitian.index')->with('status', 'Riwayat penelitian berhasil dihapus.');
    }

    private function storeUploadedFiles(Request $request, array &$validated, ?PegawaiPenelitian $row = null): void
    {
        $fields = [
            'dokumen' => 'dokumen',
            'proposal' => 'proposal',
            'lap_kemajuan' => 'lap_kemajuan',
            'lap_keuangan' => 'lap_keuangan',
            'lap_akhir' => 'lap_akhir',
        ];

        foreach ($fields as $field => $folder) {
            if ($request->hasFile($field)) {
                $oldValue = $row?->{$field};
                $validated[$field] = $request->file($field)->store('pegawai/penelitian/' . $folder, 'public');

                if ($oldValue !== null) {
                    $this->deleteStoredDocumentIfExists($oldValue, $field);
                }
            } elseif ($row === null) {
                unset($validated[$field]);
            }
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

        $publicCandidates = [
            $value,
            'assets/penelitian/' . ltrim($value, '/'),
            'uploads/penelitian/' . ltrim($value, '/'),
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

        return null;
    }

    private function deleteStoredDocumentIfExists(?string $value, string $type): void
    {
        if (empty($value)) {
            return;
        }

        $value = str_replace('\\', '/', trim($value));

        if (preg_match('/^https?:\/\//i', $value) === 1) {
            return;
        }

        $folder = $this->folderByType($type);
        $candidates = [
            $value,
            'pegawai/penelitian/' . ltrim($value, '/'),
            'pegawai/penelitian/' . $folder . '/' . ltrim($value, '/'),
        ];

        foreach ($candidates as $candidate) {
            $candidate = trim($candidate, '/');
            if ($candidate !== '' && Storage::disk('public')->exists($candidate)) {
                Storage::disk('public')->delete($candidate);
                break;
            }
        }
    }

    private function folderByType(string $type): string
    {
        $map = [
            'dokumen' => 'dokumen',
            'proposal' => 'proposal',
            'lap_kemajuan' => 'lap_kemajuan',
            'lap_keuangan' => 'lap_keuangan',
            'lap_akhir' => 'lap_akhir',
        ];

        return $map[$type] ?? 'dokumen';
    }

    private function storeAnggota(int $penelitianId, array $pegawaiIds = [], array $mahasiswaIds = []): void
    {
        PegawaiAnggotaPenelitian::where('id_penelitian', $penelitianId)->delete();
        
        foreach ($pegawaiIds as $pegawaiId) {
            PegawaiAnggotaPenelitian::create([
                'id_penelitian' => $penelitianId,
                'id_anggota' => (int) $pegawaiId,
                'jenis_anggota' => 1,
            ]);
        }
        
        foreach ($mahasiswaIds as $mahasiswaId) {
            PegawaiAnggotaPenelitian::create([
                'id_penelitian' => $penelitianId,
                'id_anggota' => (int) $mahasiswaId,
                'jenis_anggota' => 2,
            ]);
        }
    }
}
