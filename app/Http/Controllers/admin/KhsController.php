<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;

class KhsController extends Controller
{
    //
    public function index(){
        $query['title'] = "Kartu Hasil Studi (KHS) Mahasiswa";
        $query['CurrentPage'] = 'content';
        $query['data'] = Mahasiswa::all();  
        $query['status'] = [1=>'Aktif', 'Cuti', 'keluar', 'Lulus', 'Meninggal', 'DO'];
        $query['no'] = 1;
        return view('admin.khs.index', $query);
    }
    public function show(String $id){
        $query['title'] = "Kartu Hasil Studi (KHS) Mahasiswa";
        $query['CurrentPage'] = 'content';
        
        // 1. Ambil data mahasiswa
        // Menggunakan first() menggantikan row()
        $mahasiswa = DB::table('mahasiswa')->find($id);
        $nim = $mahasiswa->nim;

        // Proteksi: Jika mahasiswa tidak ditemukan, kembalikan ke halaman sebelumnya
        if (!$mahasiswa) {
            return redirect()->back()->with('error', 'Mahasiswa dengan NIM tersebut tidak ditemukan.');
        }

        // 2. Ambil data tahun ajaran aktif berdasarkan tipe mahasiswa
        $ta = DB::table('master_tahun_ajaran')
            ->where('is_aktif', 1)
            ->where('tipe_mhs', $mahasiswa->tipe_mhs)
            ->first();

        // Proteksi: Jika tidak ada tahun ajaran aktif
        if (!$ta) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif untuk tipe mahasiswa ini.');
        }

        // 3. Menentukan string jenis semester
        $jenis = '';
        if ($ta->jenis == 1) {
            $jenis = 'Ganjil';
        } elseif ($ta->jenis == 2) {
            $jenis = 'Genap';
        } elseif ($ta->jenis == 3) {
            $jenis = 'Antara Ganjil Genap';
        } elseif ($ta->jenis == 4) {
            $jenis = 'Antara Genap Ganjil';
        }

        // 4. Siapkan array data KHS
        $query['ta'] = $ta->awal . ' - ' . $ta->akhir . ' ' . $jenis;
        $query['nim'] = $nim;
        
        // Perhitungan tahun angkatan
        $angkatan = substr($nim, 2, 2);
        $tahun_angkatan = "20" . $angkatan;

        // Set Session (Menggantikan $this->session->set_userdata)
        session(['nim' => $nim]);

        // 5. Query KHS (Menggunakan Query Builder)
        $query['khs'] = DB::table('master_nilai as mn')
            ->select(
                'mn.*',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen"),
                'mjt.*',
                'mmk.nama_mata_kuliah as mata_kuliah',
                'mmk.jumlah_sks'
            )
            ->leftJoin('master_jadwal_temp as mjt', 'mjt.id', '=', 'mn.id_jadwal')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mjt.id_dosen')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->where('mn.nim', $nim)
            ->where('mn.id_tahun', $ta->id)
            ->get(); // Menggantikan result()

        // 6. Query History Tahun Ajaran Pertama (<= 2023)
        $query['ta_history'] = DB::table('master_tahun_ajaran')
            ->where('id', '<>', $ta->id)
            ->where('awal', '>=', $tahun_angkatan)
            ->where('awal', '<=', 2023)
            ->where('is_delete', '0') // Asumsi kolom is_delete bertipe string/enum sesuai DB Anda
            ->orderByDesc('id')
            ->get();

        // 7. Query History Tahun Ajaran Kedua (> 2023)
        if ($tahun_angkatan > 2023) {
            $query['ta_history2'] = DB::table('master_tahun_ajaran')
                ->where('id', '<>', $ta->id)
                ->where('awal', '>', $tahun_angkatan)
                ->where('tipe_mhs', $mahasiswa->tipe_mhs)
                ->where('is_delete', '0')
                ->orderByDesc('id')
                ->get();
        } else {
            $query['ta_history2'] = DB::table('master_tahun_ajaran')
                ->where('id', '<>', $ta->id)
                ->where('awal', '>', 2023)
                ->where('tipe_mhs', $mahasiswa->tipe_mhs)
                ->where('is_delete', '0')
                ->orderByDesc('id')
                ->get();
        }
        $query['krs_history'] = [];
        foreach($query['ta_history2'] as $ta_history){
            $ta_id = $ta_history->id;
            $jenis = '';
            
            if ($ta_history->jenis == 1) $jenis = 'Ganjil';
            elseif ($ta_history->jenis == 2) $jenis = 'Genap';
            elseif ($ta_history->jenis == 3) $jenis = 'Antara Ganjil Genap';
            elseif ($ta_history->jenis == 4) $jenis = 'Antara Genap Ganjil';            
            
            // CATATAN PENTING: Memanggil fungsi model di dalam view adalah bad practice (MVC violation).
            // Idealnya, krs_history dikirim dari Controller di dalam loop ta_history2.
            // Tapi untuk menjaga kode ini tetap jalan, saya panggil via service container:
            $query['krs_history'][$ta_history->id] = DB::table('master_nilai')
                    ->select(
                        'master_nilai.*', 
                        DB::raw("CONCAT(COALESCE(pegawai_biodata.gelar_depan, ''), ' ', COALESCE(pegawai_biodata.nama_lengkap, ''), ' ', COALESCE(pegawai_biodata.gelar_belakang, '')) as nama_dosen"), 
                        'master_jadwal.*',
                        'master_mata_kuliah.nama_mata_kuliah as mata_kuliah',
                        'master_mata_kuliah.jumlah_sks'
                    )
                    ->leftJoin('master_jadwal', 'master_jadwal.id_jadwal', '=', 'master_nilai.id_jadwal')
                    ->leftJoin('pegawai_biodata', 'pegawai_biodata.id', '=', 'master_jadwal.id_dosen')
                    ->leftJoin('master_mata_kuliah', 'master_mata_kuliah.kode_mata_kuliah', '=', 'master_jadwal.kode_mata_kuliah')
                    ->where('master_nilai.nim', $nim)
                    ->where('master_nilai.id_tahun', $ta_history->id)
                    ->where('master_jadwal.id_tahun', $ta_history->id)
                    ->get();
        }
        foreach($query['ta_history'] as $ta_history){
            $ta_id = $ta_history->id;
            $jenis = '';
            
            if ($ta_history->jenis == 1) $jenis = 'Ganjil';
            elseif ($ta_history->jenis == 2) $jenis = 'Genap';
            elseif ($ta_history->jenis == 3) $jenis = 'Antara Ganjil Genap';
            elseif ($ta_history->jenis == 4) $jenis = 'Antara Genap Ganjil';            
            
            // CATATAN PENTING: Memanggil fungsi model di dalam view adalah bad practice (MVC violation).
            // Idealnya, krs_history dikirim dari Controller di dalam loop ta_history2.
            // Tapi untuk menjaga kode ini tetap jalan, saya panggil via service container:
            $query['krs_history'][$ta_history->id] = DB::table('master_nilai')
                    ->select(
                        'master_nilai.*', 
                        DB::raw("CONCAT(COALESCE(pegawai_biodata.gelar_depan, ''), ' ', COALESCE(pegawai_biodata.nama_lengkap, ''), ' ', COALESCE(pegawai_biodata.gelar_belakang, '')) as nama_dosen"), 
                        'master_jadwal.*',
                        'master_mata_kuliah.nama_mata_kuliah as mata_kuliah',
                        'master_mata_kuliah.jumlah_sks'
                    )
                    ->leftJoin('master_jadwal', 'master_jadwal.id_jadwal', '=', 'master_nilai.id_jadwal')
                    ->leftJoin('pegawai_biodata', 'pegawai_biodata.id', '=', 'master_jadwal.id_dosen')
                    ->leftJoin('master_mata_kuliah', 'master_mata_kuliah.kode_mata_kuliah', '=', 'master_jadwal.kode_mata_kuliah')
                    ->where('master_nilai.nim', $nim)
                    ->where('master_nilai.id_tahun', $ta_history->id)
                    ->where('master_jadwal.id_tahun', $ta_history->id)
                    ->get();
        }
        return view('admin.khs.detail', $query);
    }
}
