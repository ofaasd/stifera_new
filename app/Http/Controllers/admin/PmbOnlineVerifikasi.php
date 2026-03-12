<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class PmbOnlineVerifikasi extends Controller
{
    //
    public function index(){
        $query['title'] = "Daftar Sekolah";
        $query['CurrentPage'] = 'content';
        $where = array('is_delete' => '0',
							'is_mundur' => '0',
							'is_online' => '1');
        $query['daftar_pmb'] = DB::table('pmb_peserta')->where($where)->get();
        $query['status_pembayaran'] = array("---Verifikasi Pembayaran---","Diterima","Ditolak");
        $query['status_pembayaran2'] = array("Belum Diverifikasi","Diterima","Ditolak");
        $query['no'] = 1;
        return view('admin.pmb.verifikasi_pembayaran', $query);
    }
    public function simpan_verifikasi(Request $request){
        try {
            // Eksekusi update menggunakan Query Builder Laravel
            DB::table('pmb_peserta')
                ->where('nopen', $request->input('nopen'))
                ->update([
                    'registrasi_pendaftaran' => $request->input('verifikasi')
                ]);

            // Jika berhasil, kembalikan nilai "1" (akan ditangkap oleh success: function(data) di AJAX)
            return "1";

        } catch (\Exception $e) {
            // Jika gagal (misal koneksi terputus atau nopen tidak valid), kembalikan "0"
            return "0";
            
            // Opsional: Anda juga bisa melakukan logging error-nya agar mudah dilacak
            // \Illuminate\Support\Facades\Log::error('Error Verifikasi PMB: ' . $e->getMessage());
        }
    }
}
