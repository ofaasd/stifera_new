<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterKurikulum;
use DB;

class KurikulumController extends Controller
{
    public function index()
    {
        $data['title']       = 'Daftar Kurikulum';
        $data['CurrentPage'] = 'content';
        $data['kurikulum']   = DB::table('master_kurikulum')
            ->leftJoin('program_studi', 'master_kurikulum.progdi', '=', 'program_studi.kode')
            ->select('master_kurikulum.*', 'program_studi.nama_jurusan')
            ->orderBy('master_kurikulum.id', 'desc')
            ->get();
        $data['no'] = 1;
        return view('admin.kurikulum.index', $data);
    }

    public function create()
    {
        $data['title']       = 'Tambah Kurikulum';
        $data['CurrentPage'] = 'content';
        $data['progdi']      = DB::table('program_studi')->orderBy('nama_jurusan')->get();
        return view('admin.kurikulum.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_kurikulum' => 'required|string|max:50',
            'progdi'         => 'required',
            'thn_ajar'       => 'required|string|max:10',
            'angkatan'       => 'required|string|max:10',
            'status'         => 'required|integer',
        ]);

        MasterKurikulum::create([
            'kode_kurikulum' => $request->kode_kurikulum,
            'progdi'         => $request->progdi,
            'thn_ajar'       => $request->thn_ajar,
            'angkatan'       => $request->angkatan,
            'status'         => $request->status,
            'log_update'     => now(),
        ]);

        return redirect('master/kurikulum')->with('status', 'Data Kurikulum berhasil ditambahkan!');
    }

    public function edit(string $id)
    {
        $id = base64_decode(base64_decode($id));

        $kurikulum = DB::table('master_kurikulum')->where('id', $id)->first();
        if (!$kurikulum) {
            return redirect('master/kurikulum')->with('error', 'Data tidak ditemukan.');
        }

        $data['title']      = 'Edit Kurikulum';
        $data['CurrentPage'] = 'content';
        $data['d']          = $kurikulum;
        $data['progdi']     = DB::table('program_studi')->orderBy('nama_jurusan')->get();
        return view('admin.kurikulum.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $id = base64_decode(base64_decode($id));

        $request->validate([
            'kode_kurikulum' => 'required|string|max:50',
            'progdi'         => 'required',
            'thn_ajar'       => 'required|string|max:10',
            'angkatan'       => 'required|string|max:10',
            'status'         => 'required|integer',
        ]);

        DB::table('master_kurikulum')->where('id', $id)->update([
            'kode_kurikulum' => $request->kode_kurikulum,
            'progdi'         => $request->progdi,
            'thn_ajar'       => $request->thn_ajar,
            'angkatan'       => $request->angkatan,
            'status'         => $request->status,
            'log_update'     => now(),
        ]);

        return redirect('master/kurikulum')->with('status', 'Data Kurikulum berhasil diperbarui!');
    }

    public function delete(string $id)
    {
        $id = base64_decode(base64_decode($id));

        $deleted = DB::table('master_kurikulum')->where('id', $id)->delete();

        if ($deleted) {
            return redirect('master/kurikulum')->with('status', 'Data Kurikulum berhasil dihapus.');
        }
        return redirect('master/kurikulum')->with('error', 'Gagal menghapus data.');
    }
}
