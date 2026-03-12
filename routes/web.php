<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NexadashController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
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

Route::get('/', function () {
    if (Auth::check()) {
        // Jika sudah login, arahkan ke route dashboard
        return redirect()->route('dashboard'); 
    }
    // Jika belum login, arahkan ke route login
    return redirect()->route('login');
});
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);


Route::middleware('auth')->group(function () {
    Route::get('/dashboard',[DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

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
    //resources
    Route::resource('pegawai', PegawaiController::class);
    Route::resource('mahasiswa', MahasiswaController::class);
    Route::resource('pmb/gelombang', PmbGelombangController::class);
    Route::resource('pmb', PmbController::class);
    Route::resource('master/matakuliah', MatakuliahController::class);
});
    