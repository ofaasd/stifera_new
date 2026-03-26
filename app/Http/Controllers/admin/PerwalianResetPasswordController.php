<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PerwalianResetPasswordController extends Controller
{
    public function index()
    {
        $data['title'] = 'Reset Password Pegawai';
        $data['CurrentPage'] = 'content';

        $data['pegawai'] = DB::table('pegawai as p')
            ->leftJoin('pegawai_biodata as pb', 'pb.id_pegawai', '=', 'p.id')
            ->select(
                'p.id',
                'p.npp',
                'p.usrnm',
                'p.nama',
                'p.status',
                'pb.nama_lengkap'
            )
            ->where('p.status', 1)
            ->orderBy('p.nama', 'asc')
            ->get();

        return view('admin.perwalian.reset_password_index', $data);
    }

    public function resetByNpp(string $npp)
    {
        $pegawai = DB::table('pegawai')->where('npp', $npp)->where('status', 1)->first();

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Pegawai tidak ditemukan atau tidak aktif.');
        }

        DB::table('pegawai')
            ->where('npp', $npp)
            ->update([
                'paswd' => Hash::make((string) $npp),
            ]);

        return redirect()->back()->with('status', 'Password pegawai dengan NPP ' . $npp . ' berhasil direset.');
    }
}
