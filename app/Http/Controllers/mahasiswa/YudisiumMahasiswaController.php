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
    protected function getMandatoryKeys($id_prodi)
    {
        if ($id_prodi == 1) { // D-III
            return [
                'laporan_pkl' => 'Laporan PKL',
                'agenda_pkl' => 'Agenda PKL',
                'kti' => 'KTI',
                'bimbingan_kti' => 'Bimbingan KTI',
                'bimbingan_akademik' => 'Bimbingan Akademik',
                'bebas_administrasi' => 'Bebas Administrasi',
                'bebas_perpus_lab' => 'Bebas Perpus & Lab',
                'sertifikat_osce' => 'Sertifikat OSCE',
                'sertifikat_ukom' => 'Sertifikat UKOM',
                'sertifikat_amt' => 'Sertifikat AMT (Untuk mahasiswa RPL isikan dengan sertifikat lain)'
            ];
        } else { // S-1 (default)
            return [
                'laporan_pkf' => 'Laporan PKF',
                'agenda_pkf' => 'Agenda PKF',
                'skripsi' => 'Skripsi',
                'bimbingan_skripsi' => 'Bimbingan Skripsi',
                'bimbingan_akademik' => 'Bimbingan Akademik',
                'bebas_administrasi' => 'Bebas Administrasi',
                'bebas_perpus_lab' => 'Bebas Perpus & Lab',
                'sertifikat_amt' => 'Sertifikat AMT (Untuk mahasiswa RPL isikan dengan sertifikat lain)'
            ];
        }
    }

    // List mandatory items according to the user request.
    // The keys will be used in the DB.


    public function index()
    {
        $data['CurrentPage'] = 'content'; // Ensure template loads needed JS
        $data['title'] = 'Pengajuan Yudisium';

        $mahasiswa = Auth::guard('mahasiswa')->user();
        $idMahasiswa = $mahasiswa->id;

        $activePeriode = YudisiumPeriode::where('is_active', true)
            ->where('id_program_studi', $mahasiswa->id_program_studi)
            ->whereRaw("FIND_IN_SET(?, angkatan_allowed)", [$mahasiswa->angkatan])
            ->first();

        // Coba alternatif LIKE jika DB server tidak mendukung FIND_IN_SET pada format koma spasi dengan baik:
        // Sebenarnya lebih aman pakai LIKE '%'.$mahasiswa->angkatan.'%' saja.
        if (!$activePeriode) {
            $activePeriode = YudisiumPeriode::where('is_active', true)
                ->where('id_program_studi', $mahasiswa->id_program_studi)
                ->where('angkatan_allowed', 'LIKE', '%' . $mahasiswa->angkatan . '%')
                ->first();
        }

        if (!$activePeriode) {
            return view('mahasiswa.yudisium.pengajuan', array_merge($data, [
                'periode_aktif' => false,
                'pendaftaran' => null
            ]));
        }

        $pendaftaran = YudisiumPendaftaran::with('berkas')->where('id_periode', $activePeriode->id)
            ->where('id_mahasiswa', $idMahasiswa)
            ->first();

        // Pass mandatory keys to display form or statuses
        $data['mandatoryFiles'] = $this->getMandatoryKeys($mahasiswa->id_program_studi);
        $data['jumlah_sertifikat'] = ($mahasiswa->id_program_studi == 1) ? 7 : 11;
        $data['periode_aktif'] = true;
        $data['activePeriode'] = $activePeriode;
        $data['pendaftaran'] = $pendaftaran;

        return view('mahasiswa.yudisium.pengajuan', $data);
    }

    public function store(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();

        $activePeriode = YudisiumPeriode::where('is_active', true)
            ->where('id_program_studi', $mahasiswa->id_program_studi)
            ->where('angkatan_allowed', 'LIKE', '%' . $mahasiswa->angkatan . '%')
            ->first();

        if (!$activePeriode) {
            return redirect()->back()->withErrors('Pendaftaran yudisium belum dibuka untuk prodi / angkatan Anda.');
        }

        $idMahasiswa = $mahasiswa->id;

        // Cek action (Submit final atau Draft)
        $isDraft = $request->input('action') === 'draft';

        $rules = [];
        $mandatoryKeys = $this->getMandatoryKeys($mahasiswa->id_program_studi);
        $jumlahSertifikat = ($mahasiswa->id_program_studi == 1) ? 7 : 11;

        // Ambil pendaftaran existing (kalau ada) buat cek file mana yang sudah diupload
        $existingPendaftaran = YudisiumPendaftaran::where('id_periode', $activePeriode->id)
            ->where('id_mahasiswa', $idMahasiswa)
            ->first();

        $alreadyUploaded = [];
        $sertifikatUploadedCount = 0;
        if ($existingPendaftaran) {
            $berkas = YudisiumBerkas::where('id_pendaftaran', $existingPendaftaran->id)->get();
            foreach ($berkas as $b) {
                if ($b->jenis_berkas !== 'sertifikat_kegiatan') {
                    $alreadyUploaded[] = $b->jenis_berkas;
                } else {
                    $sertifikatUploadedCount++;
                }
            }
        }

        foreach ($mandatoryKeys as $key => $label) {
            // Jika final submit dan file belum pernah diupload sebelumnya, maka wajib
            if (!$isDraft && !in_array($key, $alreadyUploaded)) {
                $rules[$key] = 'required|mimes:pdf,jpg,jpeg,png|max:25600'; // maks 25MB
            } else {
                $rules[$key] = 'nullable|mimes:pdf,jpg,jpeg,png|max:25600';
            }
        }

        $sertifSisa = $jumlahSertifikat - $sertifikatUploadedCount;
        if ($sertifSisa < 0)
            $sertifSisa = 0;

        if (!$isDraft && $sertifikatUploadedCount < $jumlahSertifikat) {
            $rules['sertifikat_kegiatan'] = 'required|array|min:' . $sertifSisa;
        } else {
            $rules['sertifikat_kegiatan'] = 'nullable|array';
        }
        $rules['sertifikat_kegiatan.*'] = 'nullable|mimes:pdf,jpg,jpeg,png|max:25600';

        $request->validate($rules, [
            'required' => ':attribute wajib diunggah',
            'mimes' => ':attribute harus berupa file PDF atau JPG/PNG',
            'max' => ':attribute tidak boleh lebih dari 25MB',
            'sertifikat_kegiatan.min' => 'Sisa wajib unggah sertifikat kegiatan: ' . $sertifSisa . ' file'
        ], $mandatoryKeys);

        // Buat Pendaftaran Baru atau Ambil
        $pendaftaran = YudisiumPendaftaran::firstOrCreate([
            'id_periode' => $activePeriode->id,
            'id_mahasiswa' => $idMahasiswa,
        ], [
            'status_pengajuan' => $isDraft ? 'draft' : 'menunggu'
        ]);

        if (!$isDraft && $pendaftaran->status_pengajuan == 'draft') {
            $pendaftaran->update(['status_pengajuan' => 'menunggu']);
        }

        // Simpan Berkas
        $destinationPath = public_path('uploads/yudisium/' . $idMahasiswa);
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        foreach ($mandatoryKeys as $key => $label) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $fileName = time() . '_' . $key . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $fileName);

                $berkas = YudisiumBerkas::where('id_pendaftaran', $pendaftaran->id)->where('jenis_berkas', $key)->first();
                if ($berkas) {
                    if (file_exists(public_path($berkas->file_path)))
                        unlink(public_path($berkas->file_path));
                    $berkas->update(['file_path' => 'uploads/yudisium/' . $idMahasiswa . '/' . $fileName]);
                } else {
                    YudisiumBerkas::create([
                        'id_pendaftaran' => $pendaftaran->id,
                        'jenis_berkas' => $key,
                        'file_path' => 'uploads/yudisium/' . $idMahasiswa . '/' . $fileName,
                        'status_berkas' => 'menunggu'
                    ]);
                }
            }
        }

        // Simpan Sertifikat Kegiatan Baru (tambahan)
        if ($request->hasFile('sertifikat_kegiatan')) {
            $files = $request->file('sertifikat_kegiatan');
            $count = 0;
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $fileName = time() . '_sertifikat_kegiatan_' . $count . '.' . $file->getClientOriginalExtension();
                    $file->move($destinationPath, $fileName);

                    YudisiumBerkas::create([
                        'id_pendaftaran' => $pendaftaran->id,
                        'jenis_berkas' => 'sertifikat_kegiatan',
                        'file_path' => 'uploads/yudisium/' . $idMahasiswa . '/' . $fileName,
                        'status_berkas' => 'menunggu'
                    ]);
                    $count++;
                }
            }
        }

        // Update Sertifikat Kegiatan Lama (replace)
        if ($request->hasFile('sertifikat_kegiatan_update')) {
            $files_update = $request->file('sertifikat_kegiatan_update');
            foreach ($files_update as $berkas_id => $file) {
                if ($file && $file->isValid()) {
                    $oldBerkas = YudisiumBerkas::where('id', $berkas_id)->where('id_pendaftaran', $pendaftaran->id)->first();
                    if ($oldBerkas) {
                        if (file_exists(public_path($oldBerkas->file_path)))
                            @unlink(public_path($oldBerkas->file_path));
                        $fileName = time() . '_sertifikat_upd_' . $berkas_id . '.' . $file->getClientOriginalExtension();
                        $file->move($destinationPath, $fileName);
                        $oldBerkas->update(['file_path' => 'uploads/yudisium/' . $idMahasiswa . '/' . $fileName]);
                    }
                }
            }
        }

        if (!$isDraft) {
            $pendaftaran->status_pengajuan = 'menunggu';
            $pendaftaran->save();
        }

        return redirect()->route('mahasiswa.yudisium.index')->with('success', 'Formulir persyaratan Yudisium berhasil dikirim.');
    }

    public function updateRevisi(Request $request)
    {
        $request->validate([
            'id_berkas' => 'required|exists:yudisium_berkas,id',
            'file_revisi' => 'required|mimes:pdf,jpg,jpeg,png|max:25600'
        ], [
            'file_revisi.max' => 'File revisi tidak boleh melebihi batas 25MB.'
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
