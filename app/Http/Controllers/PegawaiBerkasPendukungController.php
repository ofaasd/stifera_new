<?php

namespace App\Http\Controllers;

use App\Models\PegawaiBerkasPendukung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PegawaiBerkasPendukungController extends Controller
{
    private const SUPPORTED_FIELDS = ['ktp', 'kk'];

    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $berkas = PegawaiBerkasPendukung::query()
            ->where('id_pegawai', (int) $pegawai->id)
            ->first();

        return view('pegawai.berkas_pendukung.index', [
            'CurrentPage' => 'content',
            'title' => 'Berkas Pendukung',
            'berkas' => $berkas,
            'documents' => $this->buildDocuments($berkas),
        ]);
    }

    public function store(Request $request, string $jenis)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $field = $this->resolveField($jenis);

        $validated = $request->validate([
            'berkas' => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ], [
            'berkas.required' => 'File wajib dipilih.',
            'berkas.file' => 'Upload harus berupa file yang valid.',
            'berkas.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
            'berkas.max' => 'Ukuran file maksimal 4 MB.',
        ]);

        $row = PegawaiBerkasPendukung::query()->firstOrNew([
            'id_pegawai' => (int) $pegawai->id,
        ]);

        File::ensureDirectoryExists(public_path('assets/pegawai_berkas_pendukung'));

        if (!empty($row->{$field})) {
            $this->deletePhysicalFile($row->{$field});
        }

        $file = $validated['berkas'];
        $fileName = $field . '_' . $pegawai->id . '_' . time() . '_' . Str::lower(Str::random(8)) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('assets/pegawai_berkas_pendukung'), $fileName);

        $row->{$field} = $fileName;
        $row->save();

        return redirect()
            ->route('pegawai.berkas-pendukung.index')
            ->with('status', 'Berkas ' . strtoupper($field) . ' berhasil disimpan.');
    }

    public function destroy(string $jenis)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $field = $this->resolveField($jenis);

        $row = PegawaiBerkasPendukung::query()
            ->where('id_pegawai', (int) $pegawai->id)
            ->first();

        if (!$row || empty($row->{$field})) {
            return redirect()
                ->route('pegawai.berkas-pendukung.index')
                ->with('error', 'Berkas ' . strtoupper($field) . ' belum tersedia.');
        }

        $this->deletePhysicalFile($row->{$field});
        $row->{$field} = null;
        $row->save();

        return redirect()
            ->route('pegawai.berkas-pendukung.index')
            ->with('status', 'Berkas ' . strtoupper($field) . ' berhasil dihapus.');
    }

    private function resolveField(string $jenis): string
    {
        $field = Str::lower($jenis);

        if (!in_array($field, self::SUPPORTED_FIELDS, true)) {
            abort(404);
        }

        return $field;
    }

    private function buildDocuments(?PegawaiBerkasPendukung $berkas): array
    {
        $items = [];

        foreach (self::SUPPORTED_FIELDS as $field) {
            $fileName = $berkas?->{$field};
            $extension = $fileName ? Str::lower(pathinfo($fileName, PATHINFO_EXTENSION)) : null;

            $items[$field] = [
                'field' => $field,
                'label' => strtoupper($field),
                'file_name' => $fileName,
                'exists' => !empty($fileName),
                'url' => $fileName ? asset('assets/pegawai_berkas_pendukung/' . $fileName) : null,
                'is_pdf' => $extension === 'pdf',
                'is_image' => in_array($extension, ['jpg', 'jpeg', 'png'], true),
            ];
        }

        return $items;
    }

    private function deletePhysicalFile(string $fileName): void
    {
        $path = public_path('assets/pegawai_berkas_pendukung/' . $fileName);

        if (is_file($path)) {
            @unlink($path);
        }
    }
}
