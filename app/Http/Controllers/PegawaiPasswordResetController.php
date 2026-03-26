<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PegawaiPasswordResetController extends Controller
{
    public function showForm()
    {
        $CurrentPage = 'page-forgot-password';

        return view('auth.pegawai-reset-password', compact('CurrentPage'));
    }

    public function reset(Request $request)
    {
        $validated = $request->validate([
            'usrnm' => ['required', 'string', 'max:100'],
            'npp' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'usrnm.required' => 'Username wajib diisi.',
            'npp.required' => 'NPP wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $pegawai = Pegawai::query()
            ->where('usrnm', $validated['usrnm'])
            ->where('npp', $validated['npp'])
            ->where('status', 1)
            ->first();

        if (!$pegawai) {
            return back()
                ->withErrors(['usrnm' => 'Data pegawai tidak ditemukan atau tidak aktif.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $pegawai->paswd = Hash::make($validated['password']);
        $pegawai->save();

        return redirect()->route('login')->with('status', 'Password pegawai berhasil direset. Silakan login kembali.');
    }
}
