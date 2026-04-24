<?php

namespace App\Http\Controllers;

use App\Models\PegawaiRiwayatPendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class PegawaiRiwayatPendidikanController extends Controller
{
    public function index()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $riwayat = PegawaiRiwayatPendidikan::query()
            ->from('pegawai_riwayat_pendidikan as rp')
            ->leftJoin('master_universitas as mu', 'mu.id', '=', 'rp.universitas')
            ->leftJoin('master_program_studi as mp', 'mp.id', '=', 'rp.jurusan')
            ->where('rp.id_pegawai', (int) $pegawai->id)
            ->select(
                'rp.*',
                'mu.nama_universitas as universitas_nama',
                'mp.nama_jurusan as jurusan_nama'
            )
            ->orderByRaw("FIELD(rp.jenjang, 'S3','S2','S1','D4','D3','D2','D1')")
            ->orderByDesc('rp.id')
            ->get();

        $data = [
            'CurrentPage' => 'content',
            'title' => 'Riwayat Pendidikan',
            'riwayat' => $riwayat,
            'universitas' => \DB::table('master_universitas')->orderBy('nama_universitas')->get(),
            'prodi' => \DB::table('master_program_studi')->orderBy('nama_jurusan')->get(),
            'jenjangList' => ['S1', 'S2', 'S3', 'D1', 'D2', 'D3', 'D4', 'Profesi'],
            'backUrl' => (Auth::guard('admin')->check() && (int) $pegawai->id > 0)
                ? url('pegawai/' . (int) $pegawai->id . '/edit')
                : route('pegawai.home'),
        ];

        return view('pegawai.riwayat_pendidikan.index', $data);
    }

    public function store(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $validated = $request->validate([
            'jenjang' => 'required|string|max:20',
            'universitas' => 'required',
            'jurusan' => 'required',
            'tempat' => 'nullable|string|max:255',
            'no_ijazah' => 'nullable|string|max:255',
            'tanggal_ijazah' => 'nullable|date',
            'tahun' => 'nullable|digits:4',
            'jenjang_profesi' => 'nullable|string|max:100',
            'ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ]);

        $validated['id_pegawai'] = (int) $pegawai->id;

        if ($request->hasFile('ijazah') && $request->file('ijazah')->isValid()) {
            File::ensureDirectoryExists(public_path('assets/ijazah_pegawai'));
            $ext = $request->file('ijazah')->getClientOriginalExtension();
            $fileName = 'ijazah_' . $pegawai->id . '_' . time() . '.' . $ext;
            $request->file('ijazah')->move(public_path('assets/ijazah_pegawai'), $fileName);
            $validated['ijazah'] = $fileName;
        }

        PegawaiRiwayatPendidikan::create($validated);

        $redirectParams = Auth::guard('admin')->check() ? ['id_pegawai' => (int) $pegawai->id] : [];

        return redirect()->route('pegawai.riwayat-pendidikan.index', $redirectParams)->with('status', 'Riwayat pendidikan berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiRiwayatPendidikan::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        $validated = $request->validate([
            'jenjang' => 'required|string|max:20',
            'universitas' => 'required',
            'jurusan' => 'required',
            'tempat' => 'nullable|string|max:255',
            'no_ijazah' => 'nullable|string|max:255',
            'tanggal_ijazah' => 'nullable|date',
            'tahun' => 'nullable|digits:4',
            'jenjang_profesi' => 'nullable|string|max:100',
            'ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:3072',
        ]);

        if ($request->hasFile('ijazah') && $request->file('ijazah')->isValid()) {
            File::ensureDirectoryExists(public_path('assets/ijazah_pegawai'));

            if (!empty($row->ijazah)) {
                $oldPath = public_path('assets/ijazah_pegawai/' . $row->ijazah);
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $ext = $request->file('ijazah')->getClientOriginalExtension();
            $fileName = 'ijazah_' . $pegawai->id . '_' . time() . '.' . $ext;
            $request->file('ijazah')->move(public_path('assets/ijazah_pegawai'), $fileName);
            $validated['ijazah'] = $fileName;
        }

        $row->update($validated);

        $redirectParams = Auth::guard('admin')->check() ? ['id_pegawai' => (int) $pegawai->id] : [];

        return redirect()->route('pegawai.riwayat-pendidikan.index', $redirectParams)->with('status', 'Riwayat pendidikan berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) {
            abort(403);
        }

        $row = PegawaiRiwayatPendidikan::query()
            ->where('id', (int) $id)
            ->where('id_pegawai', (int) $pegawai->id)
            ->firstOrFail();

        if (!empty($row->ijazah)) {
            $oldPath = public_path('assets/ijazah_pegawai/' . $row->ijazah);
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $row->delete();

        $redirectParams = Auth::guard('admin')->check() ? ['id_pegawai' => (int) $pegawai->id] : [];

        return redirect()->route('pegawai.riwayat-pendidikan.index', $redirectParams)->with('status', 'Riwayat pendidikan berhasil dihapus.');
    }
}
