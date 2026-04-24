<?php

namespace App\Http\Middleware;

use App\Models\Pegawai;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ResolvePegawaiContext
{
    public function handle(Request $request, Closure $next): Response
    {
        // Pegawai login normal tetap memakai konteks guard pegawai.
        if (Auth::guard('pegawai')->check()) {
            return $next($request);
        }

        // Untuk admin, tentukan pegawai target dari query/form/session.
        if (Auth::guard('admin')->check()) {
            $idPegawai = (int) $request->input('id_pegawai', (int) $request->session()->get('admin_target_pegawai_id', 0));

            if ($idPegawai <= 0) {
                abort(403, 'Pilih detail pegawai terlebih dahulu untuk mengakses menu riwayat.');
            }

            $pegawai = Pegawai::query()
                ->where('id', $idPegawai)
                ->where('status', 1)
                ->first();

            if (!$pegawai) {
                abort(404, 'Pegawai tidak ditemukan atau tidak aktif.');
            }

            Auth::guard('pegawai')->setUser($pegawai);
            $request->session()->put('admin_target_pegawai_id', $idPegawai);

            return $next($request);
        }

        abort(403);
    }
}
