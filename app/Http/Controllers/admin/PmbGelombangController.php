<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class PmbGelombangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $query['title'] = "Peringkat PMDP";
        $query['CurrentPage'] = 'content';
        $query['data'] = DB::table('pmb_gelombang')->get();
        $query['pmb_ta'] = DB::table('pmb_ta')->get(); 
        $query['no'] = 1;
        return view('admin.pmb.gelombang', $query); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $query['title'] = "Tambah Gelombang";
        $query['CurrentPage'] = 'content';
        $query['ta'] = DB::table('pmb_ta')->where('is_active',1)->first(); 
        return view('admin.pmb.gelombang_create', $query); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        // 1. Ambil data input dari form menggunakan helper only()
        // Ini otomatis membentuk array asosiatif (key => value) sesuai nama input
        $data = $request->only([
            'no_gel', 
            'nama_gel', 
            'nama_gel_long', 
            'tgl_mulai', 
            'tgl_akhir', 
            'ujian', 
            'jam_ujian', 
            'pengumuman', 
            'reg_mulai', 
            'reg_akhir', 
            'tahun', 
            'jenis'
        ]);

        // 2. Tambahkan nilai default untuk semester
        $data['semester'] = 1;

        try {
            // 3. Eksekusi query insert ke tabel pmb_gelombang
            DB::table('pmb_gelombang')->insert($data);

            // 4. Redirect kembali ke halaman daftar gelombang dengan pesan sukses
            return redirect('pmb/gelombang')->with('status_delete', 'Data Gelombang baru berhasil ditambahkan!');

        } catch (\Exception $e) {
            // Jika terjadi error (misal tipe data jam/tanggal salah format)
            return redirect()->back()->with('error', 'Gagal menambahkan gelombang: ' . $e->getMessage());
        }
    }

    public function store_ta(Request $request){
        // 1. Ambil data dari form POST
        // Ambil data dari form
        $id          = $request->input('id'); // Akan null jika form tambah
        $tahun_awal  = $request->input('tahun_awal');
        $tahun_akhir = $request->input('tahun_akhir');
        $kode        = $tahun_awal . "-" . $tahun_akhir;

        try {
            DB::beginTransaction();

            // JIKA ID ADA -> LAKUKAN UPDATE
            if (!empty($id)) {
                $status = $request->input('status'); // Ambil input status dari form edit

                // Jika admin mengeset TA ini menjadi Aktif (1), nonaktifkan yang lain dulu
                if ($status == 1) {
                    DB::table('pmb_ta')
                        ->where('id', '!=', $id)
                        ->update(['is_active' => 0]);
                }

                // Lakukan update pada data yang dipilih
                DB::table('pmb_ta')
                    ->where('id', $id)
                    ->update([
                        'kode'      => $kode,
                        'awal'      => $tahun_awal,
                        'akhir'     => $tahun_akhir,
                        'is_active' => $status,
                    ]);

                $pesan = 'Data Tahun Ajaran berhasil diperbarui!';

            } 
            // JIKA ID KOSONG -> LAKUKAN INSERT BARU
            else {
                // Nonaktifkan semua TA yang lama
                DB::table('pmb_ta')->update(['is_active' => 0]);

                // Insert TA baru dan otomatis jadikan Aktif (1)
                DB::table('pmb_ta')->insert([
                    'kode'      => $kode,
                    'awal'      => $tahun_awal,
                    'akhir'     => $tahun_akhir,
                    'is_active' => 1,
                ]);

                $pesan = 'Tahun Ajaran baru berhasil ditambahkan!';
            }

            DB::commit();

            // Redirect dengan pesan sukses
            return redirect('pmb/gelombang')->with('status_delete', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect('pmb/gelombang')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    public function delete_ta(Request $request){
        $id = $request->input('id');

        try {
            // 2. Eksekusi query hapus menggunakan Query Builder
            DB::table('pmb_ta')->where('id', $id)->delete();

            // 3. Redirect kembali dengan membawa pesan sukses
            return redirect('pmb/gelombang')->with('status_delete', 'Data Tahun Ajaran berhasil dihapus!');

        } catch (\Exception $e) {
            // Jika gagal (misal karena constraint relasi database)
            return redirect('pmb/gelombang')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $query['title'] = "Tambah Gelombang";
        $query['CurrentPage'] = 'content';
        $query['pmb_ta'] = DB::table('pmb_ta')->get(); 
        $query['a'] = DB::table('pmb_gelombang')->where(['id' => $id])->first();
        return view('admin.pmb.gelombang_edit', $query); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        //
        // 1. Ambil data input dari form menggunakan helper only()
        // Ini otomatis membentuk array asosiatif (key => value) sesuai nama input
        $data = $request->only([
            'no_gel', 
            'nama_gel', 
            'nama_gel_long', 
            'tgl_mulai', 
            'tgl_akhir', 
            'ujian', 
            'jam_ujian', 
            'pengumuman', 
            'reg_mulai', 
            'reg_akhir', 
            'tahun', 
            'jenis'
        ]);

        // 2. Tambahkan nilai default untuk semester
        $data['semester'] = 1;

        try {
            // 3. Eksekusi query insert ke tabel pmb_gelombang
            DB::table('pmb_gelombang')->where('id',$id)->update($data);

            // 4. Redirect kembali ke halaman daftar gelombang dengan pesan sukses
            return redirect('pmb/gelombang')->with('status_delete', 'Data Gelombang baru berhasil diupdate!');

        } catch (\Exception $e) {
            // Jika terjadi error (misal tipe data jam/tanggal salah format)
            return redirect()->back()->with('error', 'Gagal menambahkan gelombang: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            // Eksekusi hapus data berdasarkan ID
            DB::table('pmb_gelombang')->where('id', $id)->delete();

            // Redirect kembali ke halaman list gelombang dengan pesan sukses
            return redirect('pmb/gelombang')->with('status_delete', 'Data gelombang berhasil dihapus!');

        } catch (\Exception $e) {
            // Tangkap error jika data gagal dihapus (misal karena masih dipakai di tabel lain / Foreign Key)
            return redirect('pmb/gelombang')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
