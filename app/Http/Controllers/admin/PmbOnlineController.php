<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\File;

class PmbOnlineController extends Controller
{
    //
    public function index(){
        $query['title'] = "Daftar Sekolah";
        $query['CurrentPage'] = 'content';
        $where = array('is_delete' => '0',
							'is_mundur' => '0',
							'is_online' => '1');
        $query['daftar_pmb'] = DB::table('pmb_peserta_online')->where($where)->get();
        $rek = \Illuminate\Support\Facades\DB::table('master_rekening')->get();
        $query['rek'] = $rek;
        $query['no'] = 1;
        return view('admin.pmb.online', $query);
    }
    public function lihat_rapor(String $id){
        $query['title'] = "Daftar Sekolah";
        $query['CurrentPage'] = 'content';
        $where = array('id_user' => $id);
        $query['rapor'] = DB::table('pmb_nilai_rapor')->where($where)->first();
        $query['no'] = 1;
        return view('admin.pmb.lihat_rapor', $query);
    }
    public function saveVerifikasi(Request $request)
    {

        $nopen   = $request->input('nopen');
        $user_id = $request->input('user_id');

        // 2. Proses Upload File
        $buktiName = null;
        
        if ($request->hasFile('bukti')) {
            $file      = $request->file('bukti');
            $tanggal   = now()->format('YmdHis'); // Setara date('YmdHis')
            $extension = $file->getClientOriginalExtension();
            
            // Penamaan file sesuai format Anda
            $buktiName = 'bukti_peserta_' . $user_id . $tanggal . '.' . $extension;

            // Memindahkan file ke folder public/assets/bukti/
            $file->move(public_path('assets/bukti'), $buktiName);
        }

        // 3. Siapkan Array Data
        $data = [
            'nopen'          => $nopen,
            'user_id'        => $user_id,
            'id_rekening'    => $request->input('id_bank'),
            'norek_pengirim' => $request->input('norek_pengirim'),
            'an_pengirim'    => $request->input('an_pengirim'),
            'tgl_tf'         => $request->input('tgl_tf'),
            'created_at'     => now()->format('Y-m-d'),
            'is_online'      => 1,
        ];

        // Jika ada file yg diupload, masukkan ke array update. 
        // Jika tidak ada file baru, biarkan file lama tetap utuh (tidak tertimpa null).
        if ($buktiName) {
            $data['bukti'] = $buktiName;
        }

        // 4. Proses Database dengan Transaction
        try {
            DB::beginTransaction();

            // Update is_bayar di tabel pmb_peserta_online
            DB::table('pmb_peserta_online')
                ->where('user_id', $user_id)
                ->update(['is_bayar' => 1]);

            // Update is_bayar di tabel pmb_peserta
            DB::table('pmb_peserta')
                ->where('nopen', $nopen)
                ->update(['is_bayar' => 1]);

            // Fitur Sakti Laravel: updateOrInsert
            // Otomatis mengecek apakah nopen sudah ada. Jika ada => Update, Jika tidak => Insert
            DB::table('bukti_registrasi')->updateOrInsert(
                ['nopen' => $nopen], // Kondisi (Where)
                $data                // Data yang akan diproses
            );

            DB::commit();

            // Redirect dengan pesan sukses
            return redirect('pmb_online/daftar_pmb_invalid')->with('success', 'Verifikasi pembayaran berhasil disimpan!');

        } catch (\Exception $e) {
            // Batalkan semua query jika terjadi kegagalan (misal database mati)
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage());
        }
    }
}
