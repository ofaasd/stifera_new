<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminImpersonasiController extends Controller
{
    /**
     * Admin masuk sebagai mahasiswa tertentu.
     * Sesi admin tetap tersimpan di session key impersonasi.
     */
    public function impersonate(Request $request, $id)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            abort(403, 'Unauthorized');
        }

        $mahasiswa = Mahasiswa::where('id', $id)->where('status', 1)->firstOrFail();

        // Simpan info admin ke session agar bisa kembali
        $request->session()->put('impersonasi_admin', [
            'usrnm'    => $admin->usrnm ?? $admin->username ?? '',
            'nama'     => $admin->nama ?? '',
            'login_at' => now()->toDateTimeString(),
        ]);

        // Catat ke audit log
        DB::table('admin_impersonasi_log')->insert([
            'admin_usrnm' => $admin->usrnm ?? $admin->username ?? '',
            'nim'         => $mahasiswa->nim,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'login_at'    => now(),
        ]);

        // Simpan ID log agar bisa diupdate logout_at saat keluar
        $logId = DB::table('admin_impersonasi_log')->latest('id')->value('id');
        $request->session()->put('impersonasi_log_id', $logId);

        // Login sebagai mahasiswa
        Auth::guard('mahasiswa')->login($mahasiswa);

        return redirect('/mahasiswa/home')
            ->with('impersonasi_notice', 'Anda sedang masuk sebagai mahasiswa ' . $mahasiswa->nama . ' (' . $mahasiswa->nim . '). Klik "Akhiri Impersonasi" untuk kembali ke panel admin.');
    }

    /**
     * Keluar dari sesi impersonasi dan kembali ke panel admin.
     */
    public function stop(Request $request)
    {
        $logId = $request->session()->get('impersonasi_log_id');

        if ($logId) {
            DB::table('admin_impersonasi_log')
                ->where('id', $logId)
                ->update(['logout_at' => now()]);
        }

        // Hapus sesi impersonasi mahasiswa
        Auth::guard('mahasiswa')->logout();
        $request->session()->forget(['impersonasi_admin', 'impersonasi_log_id']);

        return redirect()->route('dashboard')
            ->with('status', 'Impersonasi mahasiswa telah diakhiri. Anda kembali ke panel admin.');
    }

    /**
     * Daftar log impersonasi untuk keperluan audit admin.
     */
    public function log(Request $request)
    {
        $logs = DB::table('admin_impersonasi_log')
            ->orderByDesc('login_at')
            ->limit(500)
            ->get();

        return view('admin.impersonasi.log', [
            'CurrentPage' => 'content',
            'title'       => 'Log Impersonasi Mahasiswa',
            'logs'        => $logs,
        ]);
    }
}
