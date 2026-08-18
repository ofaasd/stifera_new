<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\YudisiumPeriode;
use App\Models\YudisiumPendaftaran;
use App\Models\YudisiumBerkas;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;

class YudisiumController extends Controller
{
    public function index()
    {
        $data['CurrentPage'] = 'content';
        $data['title'] = 'Manajemen Yudisium';

        $data['periodes'] = YudisiumPeriode::orderBy('tanggal_mulai', 'desc')->get();

        // Asumsi periode aktif hanya satu
        $activePeriode = YudisiumPeriode::where('is_active', true)->first();

        if ($activePeriode) {
            $data['pendaftars'] = YudisiumPendaftaran::with('mahasiswa')
                ->where('id_periode', $activePeriode->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $data['pendaftars'] = [];
        }

        $data['activePeriode'] = $activePeriode;

        return view('admin.yudisium.index', $data);
    }

    public function storePeriode(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date'
        ]);

        if ($request->has('is_active') && $request->is_active == 1) {
            // Nonaktifkan yang lama jika ada request aktif
            YudisiumPeriode::where('is_active', true)->update(['is_active' => false]);
        }

        YudisiumPeriode::create([
            'nama_periode' => $request->nama_periode,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_akhir' => $request->tanggal_akhir,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Periode Yudisium berhasil ditambahkan');
    }

    public function show($id)
    {
        $data['CurrentPage'] = 'content';
        $data['title'] = 'Verifikasi Berkas Yudisium';

        $data['pendaftaran'] = YudisiumPendaftaran::with(['mahasiswa', 'berkas', 'periode'])->findOrFail($id);

        // Pre-defined mandatory files format matching $request
        $data['mandatoryFiles'] = [
            'laporan_pkk' => 'Laporan PKK',
            'agenda_pkk' => 'Agenda PKK',
            'skripsi' => 'Skripsi',
            'bimbingan_skripsi' => 'Bukti Bimbingan Skripsi',
            'bimbingan_akademik' => 'Bukti Bimbingan Akademik',
            'bebas_administrasi' => 'Bebas Administrasi',
            'bebas_perpus_lab' => 'Bebas Perpustakaan & Lab',
            'sertifikat_prestasi' => 'Sertifikat Prestasi/PPM'
        ];

        return view('admin.yudisium.verifikasi', $data);
    }

    public function verifikasiBerkas(Request $request)
    {
        $request->validate([
            'id_berkas' => 'required|exists:yudisium_berkas,id',
            'status_berkas' => 'required|in:valid,tolak',
            'catatan_revisi' => 'nullable|string'
        ]);

        $berkas = YudisiumBerkas::findOrFail($request->id_berkas);
        $berkas->status_berkas = $request->status_berkas;

        if ($request->status_berkas == 'tolak') {
            $berkas->catatan_revisi = $request->catatan_revisi;
        } else {
            $berkas->catatan_revisi = null; // Clear if valid
        }

        $berkas->save();

        return redirect()->back()->with('success', 'Status berkas berhasil diupdate');
    }

    public function terimaHardcopy(Request $request)
    {
        $request->validate([
            'id_pendaftaran' => 'required|exists:yudisium_pendaftaran,id',
            'type' => 'required|in:pkk,skripsi',
            'is_accepted' => 'required|boolean'
        ]);

        $pendaftaran = YudisiumPendaftaran::findOrFail($request->id_pendaftaran);

        if ($request->type == 'pkk') {
            $pendaftaran->is_hardcopy_pkk = $request->is_accepted;
        } else {
            $pendaftaran->is_hardcopy_skripsi = $request->is_accepted;
        }

        $pendaftaran->save();

        return redirect()->back()->with('success', 'Status Hardcopy berhasil disimpan');
    }

    public function tetapkanYudisium(Request $request)
    {
        $request->validate([
            'id_pendaftaran' => 'required|exists:yudisium_pendaftaran,id',
            'no_sk_yudisium' => 'required|string'
        ]);

        $pendaftaran = YudisiumPendaftaran::findOrFail($request->id_pendaftaran);

        // Pengecekan final dari sisi backend
        if (!$pendaftaran->is_hardcopy_pkk || !$pendaftaran->is_hardcopy_skripsi) {
            return redirect()->back()->withErrors(['error' => 'Gagal menetapkan. Hardcopy PKK atau Skripsi belum diterima.']);
        }

        $semuaBerkasValid = $pendaftaran->berkas->every(function ($berkas) {
            return $berkas->status_berkas === 'valid';
        });

        if (!$semuaBerkasValid) {
            return redirect()->back()->withErrors(['error' => 'Terdapat berkas softfile yang belum valid.']);
        }

        $pendaftaran->status_pengajuan = 'lulus_yudisium';
        $pendaftaran->no_sk_yudisium = $request->no_sk_yudisium;
        $pendaftaran->tgl_yudisium = \Carbon\Carbon::now();
        $pendaftaran->save();

        return redirect()->route('admin.yudisium.index')->with('success', 'Mahasiswa telah lulus Yudisium.');
    }
}
