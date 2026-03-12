<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterPresensi;
use App\Models\MasterPertemuan;
use DB;

class PresensiController extends Controller
{
    //
    public function index(){
        $query['title'] = "Data Presensi";
        $query['CurrentPage'] = 'content';
        $query['temu'] = DB::table('master_jadwal_temp as mjt')
            ->select(
                'mjt.*',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan, ''), ' ', COALESCE(pb.nama_lengkap, ''), ' ', COALESCE(pb.gelar_belakang, '')) as nama_dosen"),
                'mmk.nama_mata_kuliah'
            )
            ->join('pegawai_biodata as pb', 'mjt.id_dosen', '=', 'pb.id')
            ->join('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->get();  
        $query['no'] = 1;
        return view('admin.presensi.index', $query);
        
    }
    public function detail_presensi(String $id){
        $query['title'] = "Detail Presensi";
        $query['CurrentPage'] = 'content';
        $query['temu'] = MasterPertemuan::where('id_jadwal',$id)->get();
        $query['id_jadwal'] = $id;
        $query['no'] = 1;
        return view('admin.presensi.detail_presensi', $query);
    }
    public function create(String $id){
        $query['title'] = "Input Presensi";
        $query['CurrentPage'] = 'content';
        $id_jadwal = DB::table('master_pertemuan')->where('id', $id)->first();
        if ($id_jadwal) {
    
            // 2. Query join menggunakan Query Builder (result() di CI menjadi get() di Laravel)
            $query['mhs'] = DB::table('master_krs_temp')
                ->select('master_krs_temp.*', 'mahasiswa.nama')
                ->join('mahasiswa', 'master_krs_temp.nim', '=', 'mahasiswa.nim') // INNER JOIN
                ->where('master_krs_temp.id_jadwal', $id_jadwal->id_jadwal)
                ->get();

            // 3. Set variabel tanggal dan ID jadwal
            $query['tgl_pertemuan'] = $id_jadwal->tgl_pertemuan;
            $query['jadwal_id']     = $id_jadwal->id_jadwal;

        } else {
            // (Opsional) Nilai default/fallback jika data master_pertemuan tidak ditemukan
            $query['mhs']           = collect(); // Return collection kosong
            $query['tgl_pertemuan'] = null;
            $query['jadwal_id']     = null;
        }

        // 4. Ambil 1 baris data memo
        $query['memos'] = DB::table('tbl_memo')->where('id_pertemuan', $id)->first();


        $query['id_pertemuan'] = $id;
        $query['no'] = 1;
        return view('admin.presensi.input_presensi', $query);
    }
    public function store(Request $request){
        // Mengambil data tunggal
        $memo = $request->input('memo');
        $sub = $request->input('sub');
        $id_pertemuan = $request->input('id_pertemuan');

        // Mengambil data array dari form
        $id_jadwal_arr = $request->input('id_jadwal', []);
        $nim_arr       = $request->input('nim', []);
        $tgl_arr       = $request->input('tgl', []);
        $status_arr    = $request->input('status', []);

        $mhs_hadir       = 0;
        $mhs_tidak_hadir = 0;

        // Pastikan ada data mahasiswa yang dikirim
        if (count($nim_arr) > 0) {
            
            try {
                // Mulai Transaksi Database
                DB::beginTransaction();

                // Looping sebanyak jumlah NIM yang dikirim
                for ($i = 0; $i < count($nim_arr); $i++) {
                    
                    $id_jadwal = $id_jadwal_arr[$i];
                    $nim       = $nim_arr[$i];
                    $tgl       = $tgl_arr[$i];
                    $status    = $status_arr[$i];

                    // Hitung jumlah kehadiran
                    if ($status == 1) {
                        $mhs_hadir++;
                    } else {
                        $mhs_tidak_hadir++;
                    }

                    // SIMPAN PRESENSI: 
                    // Parameter 1: Kunci pencarian (WHERE)
                    // Parameter 2: Data yang diupdate/diinsert
                    DB::table('master_presensi')->updateOrInsert(
                        [
                            'nim'           => $nim,
                            'id_jadwal'     => $id_jadwal,
                            'tgl_pertemuan' => $tgl
                        ],
                        [
                            'status'        => $status
                        ]
                    );
                }

                // SIMPAN MEMO PERTEMUAN
                // Sama seperti presensi, jika id_pertemuan sudah ada, maka update. Jika belum, insert.
                DB::table('tbl_memo')->updateOrInsert(
                    [
                        'id_pertemuan' => $id_pertemuan
                    ],
                    [
                        'memo'            => $memo,
                        'sub'             => $sub,
                        'mhs_hadir'       => $mhs_hadir,
                        'mhs_tidak_hadir' => $mhs_tidak_hadir
                    ]
                );

                // Simpan permanen ke database
                DB::commit();

                // Redirect dengan pesan sukses (flash data)
                return redirect('master/presensi/input/' . $id_pertemuan)
                    ->with('presensi', 'Pertemuan sudah diset');

            } catch (\Exception $e) {
                // Batalkan semua query jika terjadi error (misal koneksi terputus)
                DB::rollBack();

                return redirect('master/presensi/input/' . $id_pertemuan)
                    ->with('error', 'Pertemuan gagal diset: ' . $e->getMessage());
            }
            
        } else {
            // Jika form kosong / tidak ada data mahasiswa
            return redirect('master/presensi/input/' . $id_pertemuan)
                ->with('error', 'Tidak ada data mahasiswa untuk disimpan.');
        }
    }
}
