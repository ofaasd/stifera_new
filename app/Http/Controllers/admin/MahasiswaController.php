<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    //
    public function index(){
        $query['title'] = "Data Mahasiswa";
        $query['CurrentPage'] = 'content';
        $query['data'] = Mahasiswa::all();  
        $query['status'] = [1=>'Aktif', 'Cuti', 'keluar', 'Lulus', 'Meninggal', 'DO'];
        $query['no'] = 1;
        return view('admin.mahasiswa.index', $query);
    }
    public function edit($id = ''){
        $query['title'] = "Edit Data Mahasiswa";
        $query['CurrentPage'] = 'content';        
        if (empty($id)) {
            return redirect('mahasiswa');
        }
        $cek = DB::table('mahasiswa')->where('id', $id)->exists();
        
        if (!$cek) {
            return redirect('mahasiswa');
        }

        // 3. Persiapan Data
        $title = "Mahasiswa - Academic Portal";
        $nim = DB::table('mahasiswa')->where('id', $id)->value('nim');
        $queryDetail = "SELECT mahasiswa.*, wilayah.*, pmb_peserta.foto_peserta 
                        FROM `mahasiswa` 
                        LEFT JOIN wilayah ON mahasiswa.provinsi = wilayah.id_wil 
                                        OR mahasiswa.kokab = wilayah.id_wil 
                                        OR mahasiswa.kecamatan = wilayah.id_wil 
                        LEFT JOIN pmb_peserta ON mahasiswa.nim = pmb_peserta.nim 
                        WHERE mahasiswa.nim = ?";
        
       
        $detail = json_decode(json_encode(DB::select($queryDetail, [$nim])), true);
        
        // 5. Query Master Data
        $wilayah = DB::table('wilayah')->where('id_induk_wilayah', '000000')->get();
        $prodi = DB::table('program_studi')->where('off', 0)->get();

        // Menggabungkan semua data yang akan dikirim ke view
        $query['prodi'] = $prodi;
        $query['wilayah'] = $wilayah;
        $query['detail'] = $detail ?? null; // Ambil data pertama jika ada, atau null jika tidak ada
        
        $query['id'] = $id;
        
        return view('admin.mahasiswa.edit', $query);
    }
    public function update(Request $request, $id){
        $data = [
            'nim' => $request->nim,
            'nama' => $request->nama,
            'angkatan' => $request->angkatan,
            'email' => $request->email,
            'id_program_studi' => $request->prodi,
            'status' => $request->status,
            'provinsi' => $request->provinsi,
            'kokab' => $request->kokab,
            'kecamatan' => $request->kecamatan,
            'kelurahan' => $request->kelurahan,
            'agama' => $request->agama,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => date('Y-m-d', strtotime($request->tgl_lahir)),
            'telp' => $request->telp,
            'hp' => $request->hp,
            'hp_ortu' => $request->hp_ortu,
            'alamat_semarang' => $request->alamat_semarang,
            'alamat' => $request->alamat,
            'rt' => $request->rt,
            'rw' => $request->rw,
        ];
        Mahasiswa::where('id', $id)->update($data);
        return redirect()->back()->with('success', 'Data Mahasiswa berhasil diupdate');
    }
    public function destroy($id){
        Mahasiswa::where('id', $id)->delete();
        return redirect('mahasiswa')->with('success', 'Data Mahasiswa berhasil dihapus');   
    }
}
