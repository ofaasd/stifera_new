<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NexadashController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\PegawaiLoginController;
use App\Http\Controllers\PegawaiPasswordResetController;
use App\Http\Controllers\admin\PegawaiController;
use App\Http\Controllers\admin\WilayahController;
use App\Http\Controllers\admin\MahasiswaController;
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

Route::get('/', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('dashboard');
    }

    if (Auth::guard('pegawai')->check()) {
        return redirect()->route('pegawai.home');
    }

    return redirect()->route('admin.login');
});

Route::middleware('guest:admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.store');
    Route::post('/login', [AdminLoginController::class, 'login']);
});

Route::middleware('guest:pegawai')->group(function () {
    Route::get('/pegawai/login', [PegawaiLoginController::class, 'showLoginForm'])->name('pegawai.login');
    Route::post('/pegawai/login', [PegawaiLoginController::class, 'login'])->name('pegawai.login.store');
});

Route::get('/pegawai/reset-password', [PegawaiPasswordResetController::class, 'showForm'])->name('pegawai.password.reset.form');
Route::post('/pegawai/reset-password', [PegawaiPasswordResetController::class, 'reset'])->name('pegawai.password.reset.store');


Route::middleware('auth:pegawai')->group(function () {
    Route::get('/pegawai/home', function () {
        $pegawai = Auth::guard('pegawai')->user();

        return view('pegawai.home', [
            'CurrentPage' => 'content',
            'pegawai' => $pegawai,
        ]);
    })->name('pegawai.home');

    Route::post('/pegawai/logout', [PegawaiLoginController::class, 'logout'])->name('pegawai.logout');
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

    //Action KHS Tambahan
    Route::get('/master/khs/list_mhs', [KhsController::class, 'index']);
    Route::get('/master/khs/khs_detail/{id}', [KhsController::class, 'show']);

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
    Route::get('/master/krs/edit-krs/{id}', [KrsManagementController::class, 'editKrs']);
    Route::put('/master/krs/edit-krs/{id}', [KrsManagementController::class, 'updateKrs']);
    Route::delete('/master/krs/hapus-krs/{id}', [KrsManagementController::class, 'hapusKrs']);
    Route::post('/master/krs/update-transkrip/{tipe_mhs}', [KrsManagementController::class, 'updateTranskrip']);

    // Pengaturan Ujian
    Route::get('/master/pengaturan-ujian', [PengaturanUjianController::class, 'index']);
    Route::get('/master/pengaturan-ujian/detail/{id_jadwal}', [PengaturanUjianController::class, 'detail']);
    Route::post('/master/pengaturan-ujian/detail/{id_jadwal}', [PengaturanUjianController::class, 'save']);

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
    Route::resource('pegawai', PegawaiController::class);
    Route::resource('mahasiswa', MahasiswaController::class);
    Route::resource('pmb/gelombang', PmbGelombangController::class);
    Route::resource('pmb/soal', PmbSoalController::class)->except(['show']);
    Route::resource('pmb', PmbController::class);
    Route::resource('master/matakuliah', MatakuliahController::class);
    Route::resource('master/kurikulum', KurikulumController::class);
    Route::resource('master/rombel', RombelController::class)->except(['show']);
    Route::resource('master/ruang', MasterRuangController::class)->except(['show']);
    Route::resource('master/waktu', MasterWaktuController::class)->except(['show']);
    Route::resource('master/rumpun', MasterRumpunController::class)->except(['show']);
    Route::resource('master/fakultas', MasterFakultasController::class)->except(['show']);
    Route::resource('master/progdi', MasterProgdiController::class)->except(['show']);
    Route::resource('master/tahun', MasterTahunAjaranController::class)->except(['show']);
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
    