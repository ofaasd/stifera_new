<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class PmbBayarController extends Controller
{
    //
    public function index(){
        $query['title'] = "Data USPI";
        $query['CurrentPage'] = 'content';
        $query['data'] = DB::table('pmb_registrasi')
                            ->select(
                                'pmb_registrasi.*', 
                                'pmb_registrasi.id as ids', 
                                'pmb_peserta.*'
                            )
                            ->leftJoin('pmb_peserta', 'pmb_registrasi.nopen', '=', 'pmb_peserta.nopen')
                            ->orderBy('pmb_registrasi.nopen', 'asc')
                            ->get();
        $query['no'] = 1;
        return view('admin.pmb.bayar', $query);
    }
    public function edit(String $id){
        $dataPmb = DB::table('pmb_registrasi')
            ->select('pmb_registrasi.*', 'pmb_peserta.*')
            ->leftJoin('pmb_peserta', 'pmb_registrasi.nopen', '=', 'pmb_peserta.nopen')
            ->where('pmb_registrasi.id', $id)
            ->first();

        // Proteksi jika data tidak ditemukan
        if (!$dataPmb) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // OPTIMASI: Ambil nopen dari query pertama, tidak perlu query database lagi
        $nopen = $dataPmb->nopen;

        // 2. Ambil Riwayat Pembayaran
        // get() di Laravel mengembalikan Collection, sehingga kita tidak perlu query berulang
        $riwayatQuery = DB::table('pmb_riwayat_pembayaran')
            ->where('nopen', $nopen)
            ->orderBy('id', 'desc')
            ->get();

        // 3. Siapkan Array Data untuk dikirim ke View
        $viewData = [
            'title'   => 'Riwayat Pembayaran',
            'CurrentPage' => 'content',
            'data'    => $dataPmb,
            'cek'     => $riwayatQuery->count(),   // Setara dengan num_rows()
            'id'      => $id,
            'invoice' => $riwayatQuery->first(),   // Setara dengan row() dari riwayat
            'riwayat' => $riwayatQuery,            // Setara dengan result()
        ];

        // 4. Return langsung ke file view khusus detail
        return view('admin.pmb.bayar_detail', $viewData);
    }
    public function store(Request $request)
    {

        // 2. Ambil ID dan hitung sisa tagihan
        $id = $request->input('id');
        $dibayar = $request->input('dibayar');
        $tagihan_sebelum = $request->input('tagihan_sebelum');
        $tagihan_sekarang = $tagihan_sebelum - $dibayar;

        // 3. Ambil 'nopen' dengan efisien menggunakan method value()
        $nopen = DB::table('pmb_registrasi')->where('id', $id)->value('nopen');

        if (!$nopen) {
            return redirect()->back()->with('error', 'Data registrasi tidak ditemukan.');
        }

        try {
            // 4. Eksekusi Insert
            DB::table('pmb_riwayat_pembayaran')->insert([
                'nopen'            => $nopen,
                'tagihan_sebelum'  => $tagihan_sebelum,
                'dibayar'          => $dibayar,
                'tagihan_sekarang' => $tagihan_sekarang,
                'tgl_pembayaran'   => $request->input('tgl_pembayaran'),
                'norek_pengirim'   => $request->input('norek_pengirim'),
                'an_pengirim'      => $request->input('an_pengirim'),
                'media_pembayaran' => $request->input('media_pembayaran'),
                'keterangan'       => $request->input('keterangan'),
                
                // Pilih salah satu metode pengambilan ID user di bawah ini:
                // 'dicatat'          => session('user_id') // Jika pakai session custom CI
                'dicatat'       => Auth::id()         // Jika pakai Laravel Auth standar
            ]);

            // 5. Redirect kembali ke halaman detail
            return redirect('pmb/bayar_detail/' . $id)->with('status_delete', 'Pembayaran berhasil dicatat!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mencatat pembayaran: ' . $e->getMessage());
        }
    }
}
