<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NexadashController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\PegawaiLoginController;
use App\Http\Controllers\MahasiswaLoginController;
use App\Http\Controllers\MahasiswaKrsController;
use App\Http\Controllers\MahasiswaKhsController;
use App\Http\Controllers\MahasiswaUjianController;
use App\Http\Controllers\MahasiswaNilaiController;
use App\Http\Controllers\MahasiswaMatakuliahController;
use App\Http\Controllers\MahasiswaKeuanganController;
use App\Http\Controllers\MahasiswaPresensiController;
use App\Http\Controllers\MahasiswaAbsenController;
use App\Http\Controllers\MahasiswaProfileController;
use App\Http\Controllers\MahasiswaMasukanController;
use App\Http\Controllers\PegawaiPasswordResetController;
use App\Http\Controllers\PegawaiBiodataController;
use App\Http\Controllers\PegawaiBerkasPendukungController;
use App\Http\Controllers\PegawaiPerwalianController;
use App\Http\Controllers\PegawaiPertemuanController;
use App\Http\Controllers\PegawaiNilaiController;
use App\Http\Controllers\PegawaiPresensiController;
use App\Http\Controllers\PegawaiRiwayatPendidikanController;
use App\Http\Controllers\PegawaiRiwayatJabatanFungsionalController;
use App\Http\Controllers\PegawaiRiwayatJabatanStrukturalController;
use App\Http\Controllers\PegawaiRiwayatOrganisasiController;
use App\Http\Controllers\PegawaiRiwayatMengajarController;
use App\Http\Controllers\PegawaiRiwayatPekerjaanController;
use App\Http\Controllers\PegawaiRiwayatPenelitianController;
use App\Http\Controllers\PegawaiRiwayatBkdController;
use App\Http\Controllers\PegawaiRiwayatPengabdianController;
use App\Http\Controllers\PegawaiRiwayatKaryaIlmiahController;
use App\Http\Controllers\PegawaiRiwayatBukuController;
use App\Http\Controllers\PegawaiRiwayatHakiController;
use App\Http\Controllers\PegawaiJamKerjaDetailController;
use App\Http\Controllers\PegawaiSuratIzinController;
use App\Http\Controllers\PegawaiMeninggalkanPekerjaanController;
use App\Http\Controllers\admin\PegawaiController;
use App\Http\Controllers\admin\WilayahController;
use App\Http\Controllers\admin\MahasiswaController;
use App\Http\Controllers\admin\AdminImpersonasiController;
use App\Http\Controllers\admin\KhsController;
use App\Http\Controllers\admin\PresensiController;
use App\Http\Controllers\admin\PmbController;
use App\Http\Controllers\admin\PmbOnlineVerifikasi;
use App\Http\Controllers\admin\PmbGelombangController;
use App\Http\Controllers\admin\PmbBayarController;
use App\Http\Controllers\admin\PmbOnlineController;
use App\Http\Controllers\admin\MatakuliahController;
use App\Http\Controllers\admin\KurikulumController;
use App\Http\Controllers\admin\NilaiController;
use App\Http\Controllers\admin\JadwalController;
use App\Http\Controllers\admin\KrsManagementController;
use App\Http\Controllers\admin\PerwalianResetPasswordController;
use App\Http\Controllers\admin\PengaturanUjianController;
use App\Http\Controllers\admin\RombelController;
use App\Http\Controllers\admin\PerwalianController;
use App\Http\Controllers\admin\PmbSoalController;
use App\Http\Controllers\admin\MasterRuangController;
use App\Http\Controllers\admin\MasterWaktuController;
use App\Http\Controllers\admin\JamKerjaMasterController;
use App\Http\Controllers\admin\JamKerjaDetailController;
use App\Http\Controllers\admin\MasterRumpunController;
use App\Http\Controllers\admin\MasterFakultasController;
use App\Http\Controllers\admin\MasterProgdiController;
use App\Http\Controllers\admin\MasterTahunAjaranController;
use App\Http\Controllers\admin\KeuanganMahasiswaController;
use App\Http\Controllers\admin\SettingUserController;
use App\Http\Controllers\admin\PengumumanController;
use App\Http\Controllers\admin\AgendaController;
use App\Http\Controllers\admin\BeritaController;
use App\Http\Controllers\admin\SlideController;
use App\Http\Controllers\admin\MasukanController;
use App\Http\Controllers\admin\KuesionerController;
use App\Http\Controllers\admin\DataSupportController;
use App\Http\Controllers\admin\SuratIzin2Controller;
use App\Http\Controllers\admin\MeninggalkanPekerjaanController;

Route::get('/', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('dashboard');
    }

    if (Auth::guard('pegawai')->check()) {
        return redirect()->route('pegawai.home');
    }

    if (Auth::guard('mahasiswa')->check()) {
        return redirect()->route('mahasiswa.home');
    }

    return redirect()->route('login');
});

Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginPortal'])->name('login');
    Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.store');
    Route::post('/login', [AdminLoginController::class, 'login']);
});

Route::middleware('guest:pegawai')->group(function () {
    Route::get('/pegawai/login', [PegawaiLoginController::class, 'showLoginForm'])->name('pegawai.login');
    Route::post('/pegawai/login', [PegawaiLoginController::class, 'login'])->name('pegawai.login.store');
});

Route::middleware('guest:mahasiswa')->group(function () {
    Route::get('/mahasiswa/login', [MahasiswaLoginController::class, 'showLoginForm'])->name('mahasiswa.login');
    Route::post('/mahasiswa/login', [MahasiswaLoginController::class, 'login'])->name('mahasiswa.login.store');
});

Route::get('/pegawai/reset-password', [PegawaiPasswordResetController::class, 'showForm'])->name('pegawai.password.reset.form');
Route::post('/pegawai/reset-password', [PegawaiPasswordResetController::class, 'reset'])->name('pegawai.password.reset.store');


Route::middleware('auth:pegawai')->group(function () {
    Route::get('/pegawai/home', function () {
        $pegawai = Auth::guard('pegawai')->user();

        // Count mahasiswa perwalian
        $mahasiswaPerwalianCount = DB::table('mahasiswa')
            ->where('id_dsn_wali', (int) $pegawai->id)
            ->where('status', 1)
            ->count();

        // Get pegawai biodata untuk KRM
        $biodataId = DB::table('pegawai_biodata')
            ->select('id')
            ->where('id_pegawai', (int) $pegawai->id)
            ->value('id');

        $krmList = collect();
        if ($biodataId) {
            $krmList = DB::table('master_jadwal_temp as mjt')
                ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
                ->where(function ($q) use ($biodataId) {
                    $q->where('mjt.id_dosen', $biodataId)
                      ->orWhere('mjt.id_dosen2', $biodataId);
                })
                ->select(
                    'mjt.id',
                    'mjt.kode_mata_kuliah',
                    'mmk.nama_mata_kuliah',
                    'mmk.jumlah_sks as sks',
                    'mjt.hari',
                    'mjt.sesi',
                    'mjt.ruang',
                    'mjt.rps'
                )
                ->orderBy('mjt.kode_mata_kuliah')
                ->get();
        }

        // Get dosenInfo untuk tampilan
        $dosenInfo = DB::table('pegawai_biodata')
            ->select('nama_lengkap', 'nidn')
            ->where('id_pegawai', (int) $pegawai->id)
            ->first();

        return view('pegawai.home', [
            'CurrentPage' => 'content',
            'pegawai' => $pegawai,
            'mahasiswaPerwalianCount' => $mahasiswaPerwalianCount,
            'krmList' => $krmList,
            'dosenInfo' => $dosenInfo,
        ]);
    })->name('pegawai.home');

    Route::get('/pegawai/biodata/edit_new', [PegawaiBiodataController::class, 'edit'])->name('pegawai.biodata.edit');
    Route::put('/pegawai/biodata/edit_new', [PegawaiBiodataController::class, 'update'])->name('pegawai.biodata.update');
    Route::post('/pegawai/biodata/upload-photo', [PegawaiBiodataController::class, 'uploadPhoto'])->name('pegawai.biodata.upload-photo');
    Route::post('/pegawai/biodata/change-password', [PegawaiBiodataController::class, 'changePassword'])->name('pegawai.biodata.change-password');
    Route::get('/pegawai/biodata/cv', [PegawaiBiodataController::class, 'downloadCv'])->name('pegawai.biodata.cv');
    Route::get('/pegawai/biodata/cv-excel', [PegawaiBiodataController::class, 'downloadCvExcel'])->name('pegawai.biodata.cv-excel');
    Route::get('/akademik/perwalian/dosen', [PegawaiPerwalianController::class, 'index'])->name('pegawai.perwalian.index');
    Route::post('/akademik/perwalian/dosen/verifikasi-krs', [PegawaiPerwalianController::class, 'verifikasiKrs'])->name('pegawai.perwalian.verifikasi-krs');
    Route::get('/akademik/perwalian/dosen/get-krs', [PegawaiPerwalianController::class, 'getKrs'])->name('pegawai.perwalian.get-krs');

    Route::get('/dosen/ujian', [PegawaiNilaiController::class, 'index'])->name('pegawai.nilai.index');
    Route::get('/dosen/ujian/input/{id_jadwal}', [PegawaiNilaiController::class, 'input'])->name('pegawai.nilai.input');
    Route::post('/dosen/ujian/simpan', [PegawaiNilaiController::class, 'save'])->name('pegawai.nilai.save');
    Route::post('/dosen/ujian/save-persentase', [PegawaiNilaiController::class, 'savePersentase'])->name('pegawai.nilai.save-persentase');
    Route::post('/dosen/ujian/publish-toggle', [PegawaiNilaiController::class, 'togglePublish'])->name('pegawai.nilai.publish-toggle');
    Route::post('/dosen/ujian/validasi-toggle', [PegawaiNilaiController::class, 'toggleValidasi'])->name('pegawai.nilai.validasi-toggle');
    Route::post('/dosen/ujian/upload', [PegawaiNilaiController::class, 'upload'])->name('pegawai.nilai.upload');
    Route::get('/dosen/ujian/template', [PegawaiNilaiController::class, 'downloadTemplate'])->name('pegawai.nilai.template');

    Route::get('/dosen/presensi', [PegawaiPresensiController::class, 'index'])->name('pegawai.presensi.index');
    Route::get('/dosen/presensi/tanggal/{id}', [PegawaiPresensiController::class, 'detail'])->name('pegawai.presensi.detail');
    Route::get('/dosen/presensi/input/{id}', [PegawaiPresensiController::class, 'create'])->name('pegawai.presensi.input');
    Route::post('/dosen/presensi/simpan', [PegawaiPresensiController::class, 'store'])->name('pegawai.presensi.store');

    Route::get('/dosen/pertemuan', [PegawaiPertemuanController::class, 'index'])->name('pegawai.pertemuan.index');
    Route::get('/dosen/pertemuan/{id_jadwal}', [PegawaiPertemuanController::class, 'detail'])->name('pegawai.pertemuan.detail');
    Route::post('/dosen/pertemuan/{id_jadwal}', [PegawaiPertemuanController::class, 'save'])->name('pegawai.pertemuan.save');
    Route::post('/dosen/pertemuan/{id_jadwal}/dokumen', [PegawaiPertemuanController::class, 'uploadDokumen'])->name('pegawai.pertemuan.dokumen');
    Route::post('/dosen/pertemuan/{id_jadwal}/generate-kode', [PegawaiPertemuanController::class, 'generateKode'])->name('pegawai.pertemuan.generate-kode');
    Route::get('/dosen/pertemuan/{id_jadwal}/export-pdf', [PegawaiPertemuanController::class, 'exportPdf'])->name('pegawai.pertemuan.export-pdf');

    Route::get('/pegawai/absensi/tambah_jam_kerja_detail', [PegawaiJamKerjaDetailController::class, 'edit'])->name('pegawai.jam-kerja-detail.edit');
    Route::post('/pegawai/absensi/tambah_jam_kerja_detail', [PegawaiJamKerjaDetailController::class, 'save'])->name('pegawai.jam-kerja-detail.save');

    Route::get('/pegawai/SuratIzin/index2', [PegawaiSuratIzinController::class, 'index'])->name('pegawai.surat-izin.index2');
    Route::get('/pegawai/SuratIzin/create2', [PegawaiSuratIzinController::class, 'create'])->name('pegawai.surat-izin.create2');
    Route::post('/pegawai/SuratIzin/store2', [PegawaiSuratIzinController::class, 'store'])->name('pegawai.surat-izin.store2');
    Route::get('/pegawai/SuratIzin/{id}/edit2', [PegawaiSuratIzinController::class, 'edit'])->name('pegawai.surat-izin.edit2');
    Route::put('/pegawai/SuratIzin/{id}/update2', [PegawaiSuratIzinController::class, 'update'])->name('pegawai.surat-izin.update2');
    Route::delete('/pegawai/SuratIzin/{id}/delete2', [PegawaiSuratIzinController::class, 'destroy'])->name('pegawai.surat-izin.delete2');

    Route::get('/pegawai/MeninggalkanPekerjaan', [PegawaiMeninggalkanPekerjaanController::class, 'index'])->name('pegawai.meninggalkan-pekerjaan.index');
    Route::get('/pegawai/MeninggalkanPekerjaan/create', [PegawaiMeninggalkanPekerjaanController::class, 'create'])->name('pegawai.meninggalkan-pekerjaan.create');
    Route::post('/pegawai/MeninggalkanPekerjaan', [PegawaiMeninggalkanPekerjaanController::class, 'store'])->name('pegawai.meninggalkan-pekerjaan.store');
    Route::get('/pegawai/MeninggalkanPekerjaan/{id}/edit', [PegawaiMeninggalkanPekerjaanController::class, 'edit'])->name('pegawai.meninggalkan-pekerjaan.edit');
    Route::put('/pegawai/MeninggalkanPekerjaan/{id}', [PegawaiMeninggalkanPekerjaanController::class, 'update'])->name('pegawai.meninggalkan-pekerjaan.update');
    Route::delete('/pegawai/MeninggalkanPekerjaan/{id}', [PegawaiMeninggalkanPekerjaanController::class, 'destroy'])->name('pegawai.meninggalkan-pekerjaan.destroy');

    Route::get('/pegawai/berkasPendukung', [PegawaiBerkasPendukungController::class, 'index'])->name('pegawai.berkas-pendukung.index');
    Route::post('/pegawai/berkasPendukung/{jenis}', [PegawaiBerkasPendukungController::class, 'store'])->name('pegawai.berkas-pendukung.store');
    Route::delete('/pegawai/berkasPendukung/{jenis}', [PegawaiBerkasPendukungController::class, 'destroy'])->name('pegawai.berkas-pendukung.destroy');

    Route::get('/pegawai/riwayatPendidikan', [PegawaiRiwayatPendidikanController::class, 'index'])->name('pegawai.riwayat-pendidikan.index');
    Route::post('/pegawai/riwayatPendidikan', [PegawaiRiwayatPendidikanController::class, 'store'])->name('pegawai.riwayat-pendidikan.store');
    Route::put('/pegawai/riwayatPendidikan/{id}', [PegawaiRiwayatPendidikanController::class, 'update'])->name('pegawai.riwayat-pendidikan.update');
    Route::delete('/pegawai/riwayatPendidikan/{id}', [PegawaiRiwayatPendidikanController::class, 'destroy'])->name('pegawai.riwayat-pendidikan.destroy');

    Route::get('/pegawai/riwayatJabatanFungsional', [PegawaiRiwayatJabatanFungsionalController::class, 'index'])->name('pegawai.riwayat-jabatan-fungsional.index');
    Route::post('/pegawai/riwayatJabatanFungsional', [PegawaiRiwayatJabatanFungsionalController::class, 'store'])->name('pegawai.riwayat-jabatan-fungsional.store');
    Route::put('/pegawai/riwayatJabatanFungsional/{id}', [PegawaiRiwayatJabatanFungsionalController::class, 'update'])->name('pegawai.riwayat-jabatan-fungsional.update');
    Route::delete('/pegawai/riwayatJabatanFungsional/{id}', [PegawaiRiwayatJabatanFungsionalController::class, 'destroy'])->name('pegawai.riwayat-jabatan-fungsional.destroy');

    Route::get('/pegawai/riwayatJabatanStruktural', [PegawaiRiwayatJabatanStrukturalController::class, 'index'])->name('pegawai.riwayat-jabatan-struktural.index');
    Route::post('/pegawai/riwayatJabatanStruktural', [PegawaiRiwayatJabatanStrukturalController::class, 'store'])->name('pegawai.riwayat-jabatan-struktural.store');
    Route::put('/pegawai/riwayatJabatanStruktural/{id}', [PegawaiRiwayatJabatanStrukturalController::class, 'update'])->name('pegawai.riwayat-jabatan-struktural.update');
    Route::delete('/pegawai/riwayatJabatanStruktural/{id}', [PegawaiRiwayatJabatanStrukturalController::class, 'destroy'])->name('pegawai.riwayat-jabatan-struktural.destroy');

    Route::get('/pegawai/riwayatOrganisasi', [PegawaiRiwayatOrganisasiController::class, 'index'])->name('pegawai.riwayat-organisasi.index');
    Route::post('/pegawai/riwayatOrganisasi', [PegawaiRiwayatOrganisasiController::class, 'store'])->name('pegawai.riwayat-organisasi.store');
    Route::put('/pegawai/riwayatOrganisasi/{id}', [PegawaiRiwayatOrganisasiController::class, 'update'])->name('pegawai.riwayat-organisasi.update');
    Route::delete('/pegawai/riwayatOrganisasi/{id}', [PegawaiRiwayatOrganisasiController::class, 'destroy'])->name('pegawai.riwayat-organisasi.destroy');

    Route::get('/pegawai/riwayatMengajar', [PegawaiRiwayatMengajarController::class, 'index'])->name('pegawai.riwayat-mengajar.index');
    Route::post('/pegawai/riwayatMengajar', [PegawaiRiwayatMengajarController::class, 'store'])->name('pegawai.riwayat-mengajar.store');
    Route::put('/pegawai/riwayatMengajar/{id}', [PegawaiRiwayatMengajarController::class, 'update'])->name('pegawai.riwayat-mengajar.update');
    Route::delete('/pegawai/riwayatMengajar/{id}', [PegawaiRiwayatMengajarController::class, 'destroy'])->name('pegawai.riwayat-mengajar.destroy');

    Route::get('/pegawai/riwayatPekerjaan', [PegawaiRiwayatPekerjaanController::class, 'index'])->name('pegawai.riwayat-pekerjaan.index');
    Route::post('/pegawai/riwayatPekerjaan', [PegawaiRiwayatPekerjaanController::class, 'store'])->name('pegawai.riwayat-pekerjaan.store');
    Route::put('/pegawai/riwayatPekerjaan/{id}', [PegawaiRiwayatPekerjaanController::class, 'update'])->name('pegawai.riwayat-pekerjaan.update');
    Route::delete('/pegawai/riwayatPekerjaan/{id}', [PegawaiRiwayatPekerjaanController::class, 'destroy'])->name('pegawai.riwayat-pekerjaan.destroy');

    Route::get('/pegawai/riwayatPenelitian', [PegawaiRiwayatPenelitianController::class, 'index'])->name('pegawai.riwayat-penelitian.index');
    Route::post('/pegawai/riwayatPenelitian', [PegawaiRiwayatPenelitianController::class, 'store'])->name('pegawai.riwayat-penelitian.store');
    Route::put('/pegawai/riwayatPenelitian/{id}', [PegawaiRiwayatPenelitianController::class, 'update'])->name('pegawai.riwayat-penelitian.update');
    Route::delete('/pegawai/riwayatPenelitian/{id}', [PegawaiRiwayatPenelitianController::class, 'destroy'])->name('pegawai.riwayat-penelitian.destroy');

    Route::get('/pegawai/riwayatPengabdian', [PegawaiRiwayatPengabdianController::class, 'index'])->name('pegawai.riwayat-pengabdian.index');
    Route::post('/pegawai/riwayatPengabdian', [PegawaiRiwayatPengabdianController::class, 'store'])->name('pegawai.riwayat-pengabdian.store');
    Route::put('/pegawai/riwayatPengabdian/{id}', [PegawaiRiwayatPengabdianController::class, 'update'])->name('pegawai.riwayat-pengabdian.update');
    Route::delete('/pegawai/riwayatPengabdian/{id}', [PegawaiRiwayatPengabdianController::class, 'destroy'])->name('pegawai.riwayat-pengabdian.destroy');

    Route::get('/pegawai/riwayatKaryaIlmiah', [PegawaiRiwayatKaryaIlmiahController::class, 'index'])->name('pegawai.riwayat-karya-ilmiah.index');
    Route::post('/pegawai/riwayatKaryaIlmiah', [PegawaiRiwayatKaryaIlmiahController::class, 'store'])->name('pegawai.riwayat-karya-ilmiah.store');
    Route::put('/pegawai/riwayatKaryaIlmiah/{id}', [PegawaiRiwayatKaryaIlmiahController::class, 'update'])->name('pegawai.riwayat-karya-ilmiah.update');
    Route::delete('/pegawai/riwayatKaryaIlmiah/{id}', [PegawaiRiwayatKaryaIlmiahController::class, 'destroy'])->name('pegawai.riwayat-karya-ilmiah.destroy');

    Route::get('/pegawai/riwayatBuku', [PegawaiRiwayatBukuController::class, 'index'])->name('pegawai.riwayat-buku.index');
    Route::post('/pegawai/riwayatBuku', [PegawaiRiwayatBukuController::class, 'store'])->name('pegawai.riwayat-buku.store');
    Route::put('/pegawai/riwayatBuku/{id}', [PegawaiRiwayatBukuController::class, 'update'])->name('pegawai.riwayat-buku.update');
    Route::delete('/pegawai/riwayatBuku/{id}', [PegawaiRiwayatBukuController::class, 'destroy'])->name('pegawai.riwayat-buku.destroy');

    Route::get('/pegawai/riwayatHaki', [PegawaiRiwayatHakiController::class, 'index'])->name('pegawai.riwayat-haki.index');
    Route::post('/pegawai/riwayatHaki', [PegawaiRiwayatHakiController::class, 'store'])->name('pegawai.riwayat-haki.store');
    Route::put('/pegawai/riwayatHaki/{id}', [PegawaiRiwayatHakiController::class, 'update'])->name('pegawai.riwayat-haki.update');
    Route::delete('/pegawai/riwayatHaki/{id}', [PegawaiRiwayatHakiController::class, 'destroy'])->name('pegawai.riwayat-haki.destroy');

    Route::get('/pegawai/riwayatBkd', [PegawaiRiwayatBkdController::class, 'index']);
    Route::get('/pegawai/riwayatbkd', [PegawaiRiwayatBkdController::class, 'index'])->name('pegawai.riwayat-bkd.index');
    Route::post('/pegawai/riwayatbkd', [PegawaiRiwayatBkdController::class, 'store'])->name('pegawai.riwayat-bkd.store');
    Route::put('/pegawai/riwayatbkd/{id}', [PegawaiRiwayatBkdController::class, 'update'])->name('pegawai.riwayat-bkd.update');
    Route::delete('/pegawai/riwayatbkd/{id}', [PegawaiRiwayatBkdController::class, 'destroy'])->name('pegawai.riwayat-bkd.destroy');

    Route::post('/pegawai/logout', [PegawaiLoginController::class, 'logout'])->name('pegawai.logout');
});

Route::middleware('auth:mahasiswa')->group(function () {
    Route::get('/mahasiswa/home', function () {
        $mahasiswa = Auth::guard('mahasiswa')->user();

        $tipeMhs = (int) ($mahasiswa->tipe_mhs ?? 0);
        $tahunAktif = DB::table('master_tahun_ajaran')
            ->where('is_aktif', 1)
            ->where('tipe_mhs', $tipeMhs)
            ->orderByDesc('id')
            ->first();

        if (!$tahunAktif) {
            $tahunAktif = DB::table('master_tahun_ajaran')
                ->where('tipe_mhs', $tipeMhs)
                ->orderByDesc('id')
                ->first();
        }

        $idTahunKrs = (int) ($tahunAktif->id ?? 0);
        if ($idTahunKrs <= 0) {
            $idTahunKrs = (int) DB::table('master_krs_temp')
                ->where('nim', $mahasiswa->nim)
                ->orderByDesc('id_tahun')
                ->value('id_tahun');
        }

        $krsRows = collect();
        if ($idTahunKrs > 0) {
            $krsRows = DB::table('master_krs_temp as mkt')
                ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mkt.mata_kuliah')
                ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mkt.id_dosen')
                ->select(
                    'mkt.id',
                    'mkt.mata_kuliah',
                    'mkt.sks',
                    'mkt.hari',
                    'mkt.sesi',
                    'mkt.ruang',
                    'mkt.is_publish',
                    'mmk.nama_mata_kuliah',
                    DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
                )
                ->where('mkt.nim', $mahasiswa->nim)
                ->where('mkt.id_tahun', $idTahunKrs)
                ->orderBy('mkt.id')
                ->get();
        }

        $nilaiRowsAll = DB::table('master_nilai as mn')
            ->leftJoin('master_jadwal_temp as mjt', function ($join) {
                $join->on('mjt.id', '=', 'mn.id_jadwal')
                    ->on('mjt.id_tahun', '=', 'mn.id_tahun');
            })
            ->leftJoin('master_jadwal as mj', function ($join) {
                $join->on('mj.id_jadwal', '=', 'mn.id_jadwal')
                    ->on('mj.id_tahun', '=', 'mn.id_tahun');
            })
            ->leftJoin('master_mata_kuliah as mmk_t', 'mmk_t.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mj.kode_mata_kuliah')
            ->select(
                'mn.id_tahun',
                'mn.nhuruf',
                'mn.nakhir',
                DB::raw('COALESCE(mmk_t.jumlah_sks, mmk.jumlah_sks, 0) as jumlah_sks')
            )
            ->where('mn.nim', $mahasiswa->nim)
            ->get();

        $calculateIp = function ($rows): float {
            $totalSks = 0;
            $totalPoint = 0.0;

            foreach ($rows as $row) {
                $sks = (int) ($row->jumlah_sks ?? 0);
                if ($sks <= 0) {
                    continue;
                }

                $nilaiHuruf = strtoupper(trim((string) ($row->nhuruf ?? '')));
                if ($nilaiHuruf === '' && $row->nakhir !== null && $row->nakhir !== '' && function_exists('nmutu')) {
                    $nilaiHuruf = (string) nmutu((float) $row->nakhir);
                }

                $totalSks += $sks;
                $totalPoint += ((float) (function_exists('nbobot') ? nbobot($nilaiHuruf) : 0.0) * $sks);
            }

            return $totalSks > 0 ? ($totalPoint / $totalSks) : 0.0;
        };

        $ips = 0.0;
        if ($idTahunKrs > 0) {
            $ipsRekap = DB::table('rekap_ips')
                ->where('id_mhs', $mahasiswa->nim)
                ->where('id_ta', $idTahunKrs)
                ->orderByDesc('id')
                ->value('ips');

            if ($ipsRekap !== null && $ipsRekap !== '') {
                $ips = (float) $ipsRekap;
            } else {
                $nilaiRowsSemester = $nilaiRowsAll->where('id_tahun', $idTahunKrs)->values();
                $ips = $calculateIp($nilaiRowsSemester);
            }
        }

        $ipk = $calculateIp($nilaiRowsAll);

        return view('mahasiswa.home', [
            'CurrentPage' => 'page-login',
            'mahasiswa' => $mahasiswa,
            'ips' => $ips,
            'ipk' => $ipk,
            'krsRows' => $krsRows,
            'totalSksKrs' => (int) $krsRows->sum('sks'),
        ]);
    })->name('mahasiswa.home');

    Route::post('/mahasiswa/logout', [MahasiswaLoginController::class, 'logout'])->name('mahasiswa.logout');
    Route::post('/mahasiswa/impersonasi/stop', [AdminImpersonasiController::class, 'stop'])->name('mahasiswa.impersonasi.stop');

    Route::get('/mhs/krs', [MahasiswaKrsController::class, 'index'])->name('mahasiswa.krs.index');
    Route::get('/mhs/input_krs', [MahasiswaKrsController::class, 'index'])->name('mahasiswa.krs.input');
    Route::post('/mhs/krs', [MahasiswaKrsController::class, 'store'])->name('mahasiswa.krs.store');
    Route::get('/mhs/krs/download', [MahasiswaKrsController::class, 'download'])->name('mahasiswa.krs.download');
    Route::get('/mhs/khs', [MahasiswaKhsController::class, 'index'])->name('mahasiswa.khs.index');
    Route::get('/mhs/khs/download', [MahasiswaKhsController::class, 'downloadKhs'])->name('mahasiswa.khs.download');
    Route::get('/mhs/khs/download/{idTahun}', [MahasiswaKhsController::class, 'downloadKhs'])->name('mahasiswa.khs.download-year');
    Route::get('/mhs/ujian', [MahasiswaUjianController::class, 'index'])->name('mahasiswa.ujian.index');
    Route::get('/mhs/ujian/download/uts', [MahasiswaUjianController::class, 'downloadUts'])->name('mahasiswa.ujian.download.uts');
    Route::get('/mhs/ujian/download/uas', [MahasiswaUjianController::class, 'downloadUas'])->name('mahasiswa.ujian.download.uas');
    Route::get('/mhs/daftar_nilai', [MahasiswaNilaiController::class, 'index'])->name('mahasiswa.nilai.index');
    Route::get('/mhs/matakuliah', [MahasiswaMatakuliahController::class, 'index'])->name('mahasiswa.matakuliah.index');
    Route::get('/mhs/keuangan', [MahasiswaKeuanganController::class, 'index'])->name('mahasiswa.keuangan.index');
    Route::get('/mhs/presensi', [MahasiswaPresensiController::class, 'index'])->name('mahasiswa.presensi.index');
    Route::get('/mhs/presensi/export-pdf', [MahasiswaPresensiController::class, 'exportPdf'])->name('mahasiswa.presensi.export-pdf');
    Route::get('/mhs/absen', [MahasiswaAbsenController::class, 'index'])->name('mahasiswa.absen.index');
    Route::post('/mhs/absen/verifikasi', [MahasiswaAbsenController::class, 'verifikasi'])->name('mahasiswa.absen.verifikasi');
    Route::get('/mhs/absen/ttd', [MahasiswaAbsenController::class, 'ttd'])->name('mahasiswa.absen.ttd');
    Route::post('/mhs/absen/ttd', [MahasiswaAbsenController::class, 'simpan'])->name('mahasiswa.absen.simpan');

    Route::get('/mhs/dashboard/profile', [MahasiswaProfileController::class, 'edit'])->name('mahasiswa.profile.edit');
    Route::put('/mhs/dashboard/profile', [MahasiswaProfileController::class, 'update'])->name('mahasiswa.profile.update');
    Route::post('/mhs/dashboard/profile/photo', [MahasiswaProfileController::class, 'uploadPhoto'])->name('mahasiswa.profile.upload-photo');
    Route::get('/mhs/wilayah/children', [MahasiswaProfileController::class, 'getWilayahChildren'])->name('mahasiswa.wilayah.children');
    Route::get('/mhs/dashboard/ganti_password', [MahasiswaProfileController::class, 'editPassword'])->name('mahasiswa.password.edit');
    Route::post('/mhs/dashboard/ganti_password', [MahasiswaProfileController::class, 'updatePassword'])->name('mahasiswa.password.update');
    Route::get('/mhs/masukan', [MahasiswaMasukanController::class, 'create'])->name('mahasiswa.masukan.create');
    Route::post('/mhs/masukan', [MahasiswaMasukanController::class, 'store'])->name('mahasiswa.masukan.store');
    Route::get('/mhs/masukan/{id}', [MahasiswaMasukanController::class, 'show'])->name('mahasiswa.masukan.show');
});


Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard',[DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    // Akademik > Perwalian > Reset Password Pegawai
    Route::get('/akademik/perwalian/reset_password', [PerwalianResetPasswordController::class, 'index']);
    Route::post('/akademik/perwalian/reset_password/{npp}', [PerwalianResetPasswordController::class, 'resetByNpp']);

    // Akademik > Perwalian
    Route::get('/akademik/perwalian', [PerwalianController::class, 'index']);
    Route::get('/akademik/perwalian/cari-dosen', [PerwalianController::class, 'cariDosen']);
    Route::post('/akademik/perwalian/update-dosen-wali', [PerwalianController::class, 'updateDosenWali']);
    //Action Pegawai Tambahan
    Route::get('/pegawai/reset_password/{npp}', [PegawaiController::class, 'reset_password']);
    Route::get('/pegawai/delete_pegawai/{npp}', [PegawaiController::class, 'delete_pegawai']);
    Route::get('/pegawai/lihat_krm', [PegawaiController::class, 'lihat_krm']);
    Route::get('/pegawai/struktur', [PegawaiController::class, 'struktur']); 
    Route::post('/pegawai/get_status', [PegawaiController::class, 'get_status']);
    Route::post('/pegawai/get_homebase', [PegawaiController::class, 'get_homebase']);
    Route::post('/pegawai/get_prodi', [PegawaiController::class, 'get_prodi']);
    Route::post('/pegawai/get_jabatan', [PegawaiController::class, 'get_jabatan']);
    Route::post('/pegawai/insert1', [PegawaiController::class, 'insert1']);
    Route::post('/pegawai/save', [PegawaiController::class, 'store']);
    Route::post('/pegawai/lihat_krm', [PegawaiController::class, 'show_krm']);
    Route::post('/pegawai/save_struktur', [PegawaiController::class, 'save_struktur']);
    Route::post('/pegawai/upload-photo', [PegawaiController::class, 'uploadPhoto'])->name('pegawai.upload.photo');

    //Action KHS Tambahan
    Route::get('/master/khs/list_mhs', [KhsController::class, 'index']);
    Route::get('/master/khs/khs_detail/{id}', [KhsController::class, 'show']);
    Route::get('/master/khs/cetak_khs', [KhsController::class, 'cetak_khs']);
    Route::get('/master/khs/cetak_khs_history/{id_tahun_nim}', [KhsController::class, 'cetak_khs_history']);

    // Keuangan Mahasiswa
    Route::get('/master/keuangan', [KeuanganMahasiswaController::class, 'index']);
    Route::get('/master/keuangan_mhs', [KeuanganMahasiswaController::class, 'index']);
    Route::post('/master/keuangan/deteksi', [KeuanganMahasiswaController::class, 'detectCurrentYear']);
    Route::post('/master/keuangan/generate', [KeuanganMahasiswaController::class, 'generateData']);
    Route::post('/master/keuangan/reset', [KeuanganMahasiswaController::class, 'resetStatus']);
    Route::post('/master/keuangan/status', [KeuanganMahasiswaController::class, 'updateStatus']);

    //Action KRS Management
    Route::get('/master/krs', [KrsManagementController::class, 'index']);
    Route::get('/master/krs/download-log/{tipe_mhs}', [KrsManagementController::class, 'downloadLog']);
    Route::get('/master/krs/detail/{id_tahun}/{nim}', [KrsManagementController::class, 'showDetail']);
    Route::get('/master/krs/download/{id_tahun}/{nim}', [KrsManagementController::class, 'downloadPdf']);
    Route::get('/master/krs/edit-krs/{id}', [KrsManagementController::class, 'editKrs']);
    Route::put('/master/krs/edit-krs/{id}', [KrsManagementController::class, 'updateKrs']);
    Route::delete('/master/krs/hapus-krs/{id}', [KrsManagementController::class, 'hapusKrs']);
    Route::post('/master/krs/update-transkrip/{tipe_mhs}', [KrsManagementController::class, 'updateTranskrip']);

    // Kuesioner
    Route::get('/akademik/kuesioner', [KuesionerController::class, 'index']);
    Route::get('/akademik/kuesioner/soal/{id_tahun}', [KuesionerController::class, 'soal']);
    Route::post('/akademik/kuesioner/soal/{id_tahun}', [KuesionerController::class, 'storeSoal']);
    Route::post('/akademik/kuesioner/soal/{id_tahun}/duplicate', [KuesionerController::class, 'duplicateSoalDariSemesterSebelumnya']);
    Route::put('/akademik/kuesioner/soal/{id_tahun}/{id}', [KuesionerController::class, 'updateSoal']);
    Route::delete('/akademik/kuesioner/soal/{id_tahun}/{id}', [KuesionerController::class, 'destroySoal']);
    Route::get('/akademik/kuesioner/jawaban/{id_tahun}', [KuesionerController::class, 'jawaban']);
    Route::get('/akademik/kuesioner/rekap/{id_tahun}', [KuesionerController::class, 'rekap']);
    Route::get('/akademik/kuesioner/rekap/{id_tahun}/export-excel', [KuesionerController::class, 'exportRekapExcel']);
    Route::get('/akademik/kuesioner/rekap/{id_tahun}/export-pdf', [KuesionerController::class, 'exportRekapPdf']);

    // Data Support Download
    Route::get('/data', [DataSupportController::class, 'index'])->name('data-support');
    Route::get('/data/master-mahasiswa', [DataSupportController::class, 'masterMahasiswa'])->name('data-support.master-mahasiswa');
    Route::get('/data/master-mahasiswa/export-excel', [DataSupportController::class, 'exportMasterMahasiswaExcel'])->name('data-support.master-mahasiswa.export-excel');
    Route::get('/data/ips-mahasiswa', [DataSupportController::class, 'ipsMahasiswa'])->name('data-support.ips-mahasiswa');
    Route::get('/data/master-pegawai', [DataSupportController::class, 'masterPegawai'])->name('data-support.master-pegawai');
    Route::get('/data/master-pegawai/export-excel', [DataSupportController::class, 'exportMasterPegawaiExcel'])->name('data-support.master-pegawai.export-excel');
    Route::get('/data/krs-per-ta', [DataSupportController::class, 'krsPerTa'])->name('data-support.krs-per-ta');
    Route::get('/data/krs-per-ta/export-excel', [DataSupportController::class, 'exportKrsPerTaExcel'])->name('data-support.krs-per-ta.export-excel');

    // Pengaturan Ujian
    Route::get('/master/pengaturan-ujian', [PengaturanUjianController::class, 'index']);
    Route::get('/master/pengaturan-ujian/detail/{id_jadwal}', [PengaturanUjianController::class, 'detail']);
    Route::post('/master/pengaturan-ujian/detail/{id_jadwal}', [PengaturanUjianController::class, 'save']);
    Route::get('/master/pengaturan-ujian/kursi/{id_jadwal}', [PengaturanUjianController::class, 'kursi']);
    Route::post('/master/pengaturan-ujian/kursi/{id_jadwal}', [PengaturanUjianController::class, 'saveKursi']);

    //Action Presensi 
    Route::get('/master/presensi', [PresensiController::class, 'index']);
    Route::get('/master/presensi/tanggal/{id}', [PresensiController::class, 'detail_presensi']);
    Route::get('/master/presensi/input/{id}', [PresensiController::class, 'create']);
    Route::post('/master/presensi/simpan', [PresensiController::class, 'store']);

    //Action PMB
    Route::get('/pmb/index_nim', [PmbController::class, 'index_nim']);
    Route::get('/pmb/preview_generate_nim/{tahun}', [PmbController::class, 'preview_generate_nim']);
    Route::get('/pmb/registrasi/{id}', [PmbController::class, 'registrasi']);
    Route::get('/pmb/daftar_surat', [PmbController::class, 'daftar_surat']);
    Route::get('/pmb/statistik', [PmbController::class, 'statistik']);
    Route::get('/pmb/pmdp', [PmbController::class, 'pmdp']);
    Route::get('/pmb/uspi', [PmbController::class, 'uspi']);
    Route::get('/pmb/uspi_edit/{id}', [PmbController::class, 'uspi_edit']);
    Route::get('/pmb/bayar', [PmbBayarController::class, 'index']);
    Route::get('/pmb/bayar_detail/{id}', [PmbBayarController::class, 'edit']);
    Route::get('/pmb/daftarSekolah', [PmbController::class, 'daftarSekolah']);
    Route::get('/pmb_online/verifikasi', [PmbOnlineVerifikasi::class, 'index']);
    Route::get('/pmb_online/daftar_pmb_invalid', [PmbOnlineController::class, 'index']);
    Route::get('/pmb_online/lihat_rapor/{id}', [PmbOnlineController::class, 'lihat_rapor']);


    Route::get('/master/matakuliah/delete/{id}', [MatakuliahController::class, 'delete']);
    Route::get('/master/kurikulum/delete/{id}', [KurikulumController::class, 'delete']);
    
    
    Route::post('/pmb/req_data', [PmbController::class, 'req_data']);
    Route::post('/pmb/daftar_sekolah', [PmbController::class, 'daftar_sekolah']);
    Route::post('/pmb/daftar_mou', [PmbController::class, 'daftar_mou']);
    Route::post('/pmb/nopen_gel', [PmbController::class, 'nopen_gel']);
    Route::post('/pmb/cek_nik', [PmbController::class, 'cek_nik']);
    Route::post('/pmb/save_verifikasi', [PmbController::class, 'saveVerifikasi']);
    Route::post('/pmb/tahap_1', [PmbController::class, 'tahap_1']);
    Route::post('/pmb/tahap_2', [PmbController::class, 'tahap_2']);
    Route::post('/pmb/tahap_3', [PmbController::class, 'tahap_3']);
    Route::post('/pmb/tahap_4', [PmbController::class, 'tahap_4']);
    Route::post('/pmb/surat_pernyataan2', [PmbController::class, 'surat_pernyataan2']);
    Route::post('/pmb/surat_pengumuman', [PmbController::class, 'surat_pengumuman']);
    Route::post('/pmb/uspi_update', [PmbController::class, 'uspi_update']);
    Route::post('/pmb/bayar', [PmbBayarController::class, 'store']);
    Route::post('/pmb_online/simpan_verifikasi', [PmbOnlineVerifikasi::class, 'simpan_verifikasi']);
    Route::post('/pmb_online/saveVerifikasi', [PmbOnlineController::class, 'saveVerifikasi']);



    //Action Gelombang
    Route::post('/pmb/gelombang_ta', [PmbGelombangController::class, 'store_ta']);
    Route::post('/pmb/gelombang_ta_update', [PmbGelombangController::class, 'store_ta']);
    Route::post('/pmb/gelombang_ta_hapus', [PmbGelombangController::class, 'delete_ta']);
    
    
    //Action Wilayah 
    Route::post('/wilayah/get_kota_kecamatan', [WilayahController::class, 'get_kota_kecamatan']);

    // Alias menu pengumuman lama
    Route::get('/artikel/post/1', [PengumumanController::class, 'index']);

    // Agenda (kategori = 2)
    Route::get('/artikel/post/2', [AgendaController::class, 'index'])->name('agenda.index');
    Route::get('/artikel/post/2/create', [AgendaController::class, 'create'])->name('agenda.create');
    Route::post('/artikel/post/2', [AgendaController::class, 'store'])->name('agenda.store');
    Route::get('/artikel/post/2/{id}/edit', [AgendaController::class, 'edit'])->name('agenda.edit');
    Route::put('/artikel/post/2/{id}', [AgendaController::class, 'update'])->name('agenda.update');
    Route::delete('/artikel/post/2/{id}', [AgendaController::class, 'destroy'])->name('agenda.destroy');

    // Masukan
    Route::get('/master/masukan', [MasukanController::class, 'index'])->name('masukan.index');
    Route::get('/master/masukan/create', [MasukanController::class, 'create'])->name('masukan.create');
    Route::post('/master/masukan', [MasukanController::class, 'store'])->name('masukan.store');
    Route::get('/master/masukan/{id}/edit', [MasukanController::class, 'edit'])->name('masukan.edit');
    Route::put('/master/masukan/{id}', [MasukanController::class, 'update'])->name('masukan.update');
    Route::delete('/master/masukan/{id}', [MasukanController::class, 'destroy'])->name('masukan.destroy');

    // Slideshow
    Route::get('/slide', [SlideController::class, 'index'])->name('slide.index');
    Route::get('/slide/create', [SlideController::class, 'create'])->name('slide.create');
    Route::post('/slide', [SlideController::class, 'store'])->name('slide.store');
    Route::get('/slide/{id}/edit', [SlideController::class, 'edit'])->name('slide.edit');
    Route::put('/slide/{id}', [SlideController::class, 'update'])->name('slide.update');
    Route::delete('/slide/{id}', [SlideController::class, 'destroy'])->name('slide.destroy');

    // Berita (kategori = 3)
    Route::get('/artikel/post/3', [BeritaController::class, 'index'])->name('berita.index');
    Route::get('/artikel/post/3/create', [BeritaController::class, 'create'])->name('berita.create');
    Route::post('/artikel/post/3', [BeritaController::class, 'store'])->name('berita.store');
    Route::get('/artikel/post/3/{id}/edit', [BeritaController::class, 'edit'])->name('berita.edit');
    Route::put('/artikel/post/3/{id}', [BeritaController::class, 'update'])->name('berita.update');
    Route::delete('/artikel/post/3/{id}', [BeritaController::class, 'destroy'])->name('berita.destroy');

    // Setting User
    Route::get('/SettingUser/{id}/password', [SettingUserController::class, 'editPassword']);
    Route::put('/SettingUser/{id}/password', [SettingUserController::class, 'updatePassword']);

    //resources
    Route::get('simpeg/absensi/jam_kerja_master/{id_jam_kerja}/detail', [JamKerjaDetailController::class, 'index']);
    Route::get('simpeg/absensi/jam_kerja_master/{id_jam_kerja}/detail/create', [JamKerjaDetailController::class, 'create']);
    Route::post('simpeg/absensi/jam_kerja_master/{id_jam_kerja}/detail', [JamKerjaDetailController::class, 'store']);
    Route::get('simpeg/absensi/jam_kerja_master/{id_jam_kerja}/detail/{id}/edit', [JamKerjaDetailController::class, 'edit']);
    Route::put('simpeg/absensi/jam_kerja_master/{id_jam_kerja}/detail/{id}', [JamKerjaDetailController::class, 'update']);
    Route::delete('simpeg/absensi/jam_kerja_master/{id_jam_kerja}/detail/{id}', [JamKerjaDetailController::class, 'destroy']);

    Route::resource('pegawai', PegawaiController::class);
    Route::resource('mahasiswa', MahasiswaController::class);
    Route::post('mahasiswa/{id}/reset-password', [MahasiswaController::class, 'resetPassword'])->name('mahasiswa.reset-password');
    Route::post('mahasiswa/{id}/impersonate', [AdminImpersonasiController::class, 'impersonate'])->name('admin.impersonate');
    Route::get('admin/impersonasi/log', [AdminImpersonasiController::class, 'log'])->name('admin.impersonasi.log');
    Route::resource('pmb/gelombang', PmbGelombangController::class);
    Route::resource('pmb/soal', PmbSoalController::class)->except(['show']);
    Route::resource('pmb', PmbController::class);
    Route::resource('master/matakuliah', MatakuliahController::class);
    Route::resource('master/kurikulum', KurikulumController::class);
    Route::resource('master/rombel', RombelController::class)->except(['show']);
    Route::resource('master/ruang', MasterRuangController::class)->except(['show']);
    Route::resource('master/waktu', MasterWaktuController::class)->except(['show']);
    Route::resource('simpeg/absensi/jam_kerja_master', JamKerjaMasterController::class)->except(['show']);
    Route::resource('master/rumpun', MasterRumpunController::class)->except(['show']);
    Route::resource('master/fakultas', MasterFakultasController::class)->except(['show']);
    Route::resource('master/progdi', MasterProgdiController::class)->except(['show']);
    Route::resource('master/tahun', MasterTahunAjaranController::class)->except(['show']);
    Route::resource('simpeg/SuratIzin2', SuratIzin2Controller::class)->except(['show']);
    Route::resource('simpeg/MeninggalkanPekerjaan', MeninggalkanPekerjaanController::class)->except(['show']);
    Route::resource('SettingUser', SettingUserController::class)->except(['show']);
    Route::resource('pengumuman', PengumumanController::class)->except(['show']);
    Route::resource('master/jadwal/rombel', RombelController::class)
        ->except(['show'])
        ->names('jadwal.rombel');

    // Jadwal
    Route::get('/master/jadwal', [JadwalController::class, 'index']);
    Route::get('/master/pertemuan', [JadwalController::class, 'pertemuanIndex']);
    Route::get('/master/pertemuan/{id_jadwal}', [JadwalController::class, 'pertemuanDetail']);
    Route::post('/master/pertemuan/{id_jadwal}', [JadwalController::class, 'pertemuanSave']);
    Route::post('/master/pertemuan/{id_jadwal}/dokumen', [JadwalController::class, 'pertemuanUploadDokumen']);
    Route::get('/master/pertemuan/{id_jadwal}/export-pdf', [JadwalController::class, 'pertemuanExportPdf']);
    Route::get('/master/jadwal_krs', [JadwalController::class, 'krsIndex']);
    Route::post('/master/jadwal_krs/toggle', [JadwalController::class, 'krsToggle']);
    Route::get('/master/jadwal/input/{kodeMataKuliah}', [JadwalController::class, 'create']);
    Route::post('/master/jadwal/input/{kodeMataKuliah}', [JadwalController::class, 'store']);
    Route::get('/master/jadwal/edit/{id}', [JadwalController::class, 'edit']);
    Route::put('/master/jadwal/edit/{id}', [JadwalController::class, 'update']);
    Route::delete('/master/jadwal/delete/{id}', [JadwalController::class, 'destroy']);
    Route::post('/master/jadwal/pindah-history', [JadwalController::class, 'pindahKeHistory']);

    // Input Nilai
    Route::get('/master/nilai', [NilaiController::class, 'index']);
    Route::get('/master/nilai/jadwal/{id_dosen}', [NilaiController::class, 'jadwal']);
    Route::get('/master/nilai/input/{id_jadwal}', [NilaiController::class, 'input']);
    Route::post('/master/nilai/save', [NilaiController::class, 'save']);
    Route::post('/master/nilai/save-persentase', [NilaiController::class, 'savePersentase']);
    Route::post('/master/nilai/publish-toggle', [NilaiController::class, 'togglePublish']);
    Route::post('/master/nilai/validasi-toggle', [NilaiController::class, 'toggleValidasi']);
    Route::post('/master/nilai/upload', [NilaiController::class, 'upload']);
    Route::get('/master/nilai/template', [NilaiController::class, 'downloadTemplate']);
});
    