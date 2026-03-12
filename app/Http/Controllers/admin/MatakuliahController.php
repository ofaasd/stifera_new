<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterMataKuliah;
use DB;

class MatakuliahController extends Controller
{
    //
    public function index(){
        // 1. Menggantikan fungsi v_matakuliah() menggunakan Query Builder Join
        $matakuliahList = DB::table('master_mata_kuliah')
            ->join('master_rumpun', 'master_mata_kuliah.rumpun_mata_kuliah', '=', 'master_rumpun.id')
            ->select('master_mata_kuliah.*', 'master_rumpun.nama_rumpun as rumpun')
            ->orderBy('master_mata_kuliah.semester', 'asc')
            ->get();

        // 2. Mengambil data master_rumpun dengan status = 1
        $rumpun = DB::table('master_rumpun')
            ->where('status', 1)
            ->get();

        // 3. Mengambil kelompok_matakuliah dan langsung mengubahnya jadi array [id => nama_kelompok]
        // Fungsi pluck() otomatis memetakan data persis seperti foreach buatanmu di CI
        $kelompok_matakuliah = DB::table('master_kelompok_mata_kuliah')
            ->pluck('nama_kelompok', 'id')
            ->toArray();

        // Bungkus semua data untuk di-passing ke View
        $data = [
            'title'               => 'Daftar Mata Kuliah',
            'CurrentPage'         => 'content',
            'matakuliah'          => $matakuliahList,
            'rumpun'              => $rumpun,
            'kelompok_matakuliah' => $kelompok_matakuliah
        ];
        return view('admin.matakuliah.index', $data);
    }
    public function create(){
        $matakuliah['rumpun'] = DB::table('master_rumpun')->where('status', 1)->get();
        $matakuliah['kelompok_matakuliah'] = DB::table('master_kelompok_mata_kuliah')->get();
        $matakuliah['program_studi'] = DB::table('program_studi')->get();
        $matakuliah['title'] = 'Tambah Mata Kuliah';
        $matakuliah['CurrentPage'] = 'content';
        return view('admin.matakuliah.create', $matakuliah);
        
    }
    public function store(Request $request){
        $data = [
            'kode_mata_kuliah'     => $request->input('kode_mata_kuliah'),
            'nama_mata_kuliah'     => $request->input('nama_mata_kuliah'),
            'nama_mata_kuliah_eng' => $request->input('nama_mata_kuliah_eng'),
            'jumlah_sks'           => $request->input('sks'),
            'semester'             => $request->input('smt'),
            'tp'                   => $request->input('tp'),
            'kelompok_mata_kuliah' => $request->input('kel_mata_kuliah'),
            'rumpun_mata_kuliah'   => $request->input('rumpun_mata_kuliah'),
            'is_aktif'             => $request->input('status'),
            'id_program_studi'     => $request->input('program_studi'),
        ];

        // Menyimpan data ke database
        $r = DB::table('master_mata_kuliah')->insert($data);

        // Contoh mengembalikan ke halaman sebelumnya dengan pesan sukses
        if ($r) {
            return redirect()->back()->with('status', 'Data Mata Kuliah berhasil ditambahkan!');
        }
    }
    public function edit(String $id){
        $id = base64_decode(base64_decode($id));

        // 2. Ambil data spesifik mata kuliah
        $matakuliahData = DB::table('master_mata_kuliah')->where('id', $id)->first();

        // 3. Ambil data relasi untuk dropdown
        $kelompok_matakuliah = DB::table('master_kelompok_mata_kuliah')->get();
        $rumpun              = DB::table('master_rumpun')->where('status', 1)->get();
        $program_studi       = DB::table('program_studi')->get();

        // 4. Masukkan ke dalam array untuk dilempar ke view
        $data = [
            'title'               => 'Edit Mata Kuliah',
            'CurrentPage'         => 'content',
            'd'                     => $matakuliahData,
            'kelompok_matakuliah' => $kelompok_matakuliah,
            'rumpun'              => $rumpun,
            'program_studi'       => $program_studi,
        ];

        // Sesuaikan path view dengan struktur folder resources/views kamu
        return view('admin.matakuliah.edit', $data);
    }
    public function update(String $id, Request $request){
        
        $data = [
            'kode_mata_kuliah'     => $request->input('kode_mata_kuliah'),
            'nama_mata_kuliah'     => $request->input('nama_mata_kuliah'),
            'nama_mata_kuliah_eng' => $request->input('nama_mata_kuliah_eng'),
            'jumlah_sks'           => $request->input('sks'),
            'semester'             => $request->input('smt'),
            'tp'                   => $request->input('tp'),
            'kelompok_mata_kuliah' => $request->input('kel_mata_kuliah'),
            'rumpun_mata_kuliah'   => $request->input('rumpun_mata_kuliah'),
            'is_aktif'             => $request->input('status'),
            'id_program_studi'     => $request->input('program_studi'),
        ];

        // Melakukan update berdasarkan ID yang dikirim dari form hidden
        $r = DB::table('master_mata_kuliah')
            ->where('id', $id)
            ->update($data);

        // Contoh mengembalikan ke halaman sebelumnya dengan pesan sukses
        if ($r) {
            return redirect()->back()->with('status', 'Data Mata Kuliah berhasil ditambahkan!');
        }
    }
    public function delete(String $id){
        $id = base64_decode(base64_decode($id));
        $deleted = DB::table('master_mata_kuliah')->where('id', $id)->delete();
        if ($deleted) {
            // melempar session flash dengan key 'status'
            return redirect('master/matakuliah')->with('status', 'Data Matakuliah berhasil dihapus.');
        } else {
            // melempar session flash dengan key 'error'
            return redirect('master/matakuliah')->with('errors', 'Data Matakuliah gagal dihapus.');
        }
    }
}
