<?php

namespace App\Http\Controllers\mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\YudisiumPeriode;
use App\Models\YudisiumPendaftaran;
use App\Models\YudisiumBerkas;
use Illuminate\Support\Facades\Auth;

class YudisiumMahasiswaController extends Controller
{
    // List mandatory items according to the user request.
    // The keys will be used in the DB.
    protected $mandatoryKeys = [
        'laporan_pkk' => 'Laporan PKK',
        'agenda_pkk' => 'Agenda PKK',
        'skripsi' => 'Skripsi',
        'bimbingan_skripsi' => 'Bimbingan Skripsi',
        'bimbingan_akademik' => 'Bimbingan Akademik',
        'bebas_administrasi' => 'Bebas Administrasi',
        'bebas_perpus_lab' => 'Bebas Perpus & Lab',
        'sertifikat_amt' => 'Sertifikat AMT'
    ];

    public function index()
    {
        $data['CurrentPage'] = 'content'; // Ensure template loads needed JS
        $data['title'] = 'Pengajuan Yudisium';

        $activePeriode = YudisiumPeriode::where('is_active', true)->first();
        if (!$activePeriode) {
            return view('mahasiswa.yudisium.pengajuan', array_merge($data, [
                'periode_aktif' => false,
                'pendaftaran' => null
            ]));
        }

        $idMahasiswa = Auth::guard('mahasiswa')->user()->id;

        $pendaftaran = YudisiumPendaftaran::with('berkas')->where('id_periode', $activePeriode->id)
            ->where('id_mahasiswa', $idMahasiswa)
            ->first();

        // Pass mandatory keys to display form or statuses
        $data['mandatoryFiles'] = $this->mandatoryKeys;
        $data['periode_aktif'] = true;
        $data['activePeriode'] = $activePeriode;
        $data['pendaftaran'] = $pendaftaran;

        return view('mahasiswa.yudisium.pengajuan', $data);
    }

    public function store(Request $request)
    {
        $activePeriode = YudisiumPeriode::where('is_active', true)->first();
        if (!$activePeriode) {
            return redirect()->back()->withErrors('Pendaftaran yudisium belum dibuka.');
        }

        $idMahasiswa = Auth::guard('mahasiswa')->user()->id;

        // Validasi
        $rules = [];
        foreach ($this->mandatoryKeys as $key => $label) {
            $rules[$key] = 'required|mimes:pdf,jpg,jpeg,png|max:2048'; // maks 2MB
        }
        $rules['sertifikat_tambahan.*'] = 'nullable|mimes:pdf,jpg,jpeg,png|max:2048';

        $request->validate($rules, [
            'required' => ':attribute wajib diunggah',
            'mimes' => ':attribute harus berupa file PDF atau JPG/PNG'
        ], $this->mandatoryKeys);

        // Buat Pendaftaran Baru
        $pendaftaran = YudisiumPendaftaran::firstOrCreate([
            'id_periode' => $activePeriode->id,
            'id_mahasiswa' => $idMahasiswa,
        ], [
            'status_pengajuan' => 'menunggu'
        ]);

        // Simpan Berkas Mandatory
        $destinationPath = public_path('uploads/yudisium/' . $idMahasiswa);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        foreach ($this->mandatoryKeys as $key => $label) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $fileName = time() . '_' . $key . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $fileName);

                YudisiumBerkas::create([
                    'id_pendaftaran' => $pendaftaran->id,
                    'jenis_berkas' => $key,
                    'file_path' => 'uploads/yudisium/' . $idMahasiswa . '/' . $fileName,
                    'status_berkas' => 'menunggu'
                ]);
            }
        }

        // Simpan Sertifikat Tambahan (jika ada)
        if ($request->hasFile('sertifikat_tambahan')) {
            $files = $request->file('sertifikat_tambahan');
            $count = 0;
            foreach ($files as $file) {
                if ($count >= 11)
                    break; // Maksimal 11 list
                if ($file && $file->isValid()) {
                    $fileName = time() . '_sertifikat_tambahan_' . $count . '.' . $file->getClientOriginalExtension();
                    $file->move($destinationPath, $fileName);

                    YudisiumBerkas::create([
                        'id_pendaftaran' => $pendaftaran->id,
                        'jenis_berkas' => 'sertifikat_tambahan',
                        'file_path' => 'uploads/yudisium/' . $idMahasiswa . '/' . $fileName,
                        'status_berkas' => 'menunggu'
                    ]);
                    $count++;
                }
            }
        }

        // Update overall status to menunggu if it's currently somehow different but we re-uploaded?
        // Usually, first upload means it's 'menunggu'.
        $pendaftaran->status_pengajuan = 'menunggu';
        $pendaftaran->save();

        return redirect()->route('mahasiswa.yudisium.index')->with('success', 'Formulir persyaratan Yudisium berhasil dikirim.');
    }

    public function updateRevisi(Request $request)
    {
        $request->validate([
            'id_berkas' => 'required|exists:yudisium_berkas,id',
            'file_revisi' => 'required|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        $idMahasiswa = Auth::guard('mahasiswa')->user()->id;
        $berkas = YudisiumBerkas::findOrFail($request->id_berkas);

        // Keamanan: Pastikan berkas ini benar milik pendaftar yg login
        if ($berkas->pendaftaran->id_mahasiswa != $idMahasiswa) {
            return redirect()->back()->withErrors('Akses penolakan berkas tidak diizinkan.');
        }

        if ($berkas->status_berkas !== 'tolak') {
            return redirect()->back()->withErrors('Berkas ini tidak sedang dalam status ditolak/revisi.');
        }

        $destinationPath = public_path('uploads/yudisium/' . $idMahasiswa);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $file = $request->file('file_revisi');
        // Gunakan uniqid agar tidak tabrakan waktu cache
        $fileName = time() . '_' . uniqid() . '_revisi_' . $berkas->jenis_berkas . '.' . $file->getClientOriginalExtension();
        $file->move($destinationPath, $fileName);

        // Hapus file lama jika ada
        if (file_exists(public_path($berkas->file_path))) {
            unlink(public_path($berkas->file_path));
        }

        // Update status berkas
        $berkas->file_path = 'uploads/yudisium/' . $idMahasiswa . '/' . $fileName;
        $berkas->status_berkas = 'menunggu';
        // Clear catatan karena sudah direvisi
        $berkas->catatan_revisi = null;
        $berkas->save();

        // Update keseluruhan pendaftaran ke "menunggu" agar Admin verifikasi lagi (atau "revisi")
        $pendaftaran = $berkas->pendaftaran;

        // Pengecekan jika sudah tidak ada berkas yang ditolak, status keseluruhan menyesuaikan
        $adaBerkasDitolakLagi = $pendaftaran->berkas()->where('status_berkas', 'tolak')->exists();
        if (!$adaBerkasDitolakLagi) {
            $pendaftaran->status_pengajuan = 'menunggu';
        } else {
            $pendaftaran->status_pengajuan = 'revisi'; // Masih ada revisi
        }
        $pendaftaran->save();

        return redirect()->back()->with('success', 'Berkas yang ditolak berhasil direvisi & dikirim ulang.');
    }
}
