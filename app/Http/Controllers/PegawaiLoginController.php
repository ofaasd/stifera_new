<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PegawaiLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('pegawai')->check()) {
            return redirect()->route('pegawai.home');
        }

        $CurrentPage = 'page-login';

        return view('auth.pegawai-login', compact('CurrentPage'));
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'usrnm' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string'],
        ], [
            'usrnm.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $pegawai = Pegawai::query()
            ->where('usrnm', $validated['usrnm'])
            ->where('status', 1)
            ->first();

        if (!$pegawai) {
            return back()->withErrors([
                'usrnm' => 'Akun pegawai tidak ditemukan atau tidak aktif.',
            ])->onlyInput('usrnm');
        }

        $passwordInput = $validated['password'];
        $storedHash = (string) ($pegawai->paswd ?? '');

        $isLaravelHashValid = !empty($storedHash) && Hash::check($passwordInput, $storedHash);
        $isLegacyMd5Valid = strlen($storedHash) === 32 && ctype_xdigit($storedHash) && hash_equals(strtolower($storedHash), md5($passwordInput));

        if (!$isLaravelHashValid && !$isLegacyMd5Valid) {
            return back()->withErrors([
                'usrnm' => 'Username atau password pegawai tidak valid.',
            ])->onlyInput('usrnm');
        }

        // Upgrade hash lama MD5 saat login berhasil pertama kali.
        if ($isLegacyMd5Valid) {
            $pegawai->paswd = Hash::make($passwordInput);
            $pegawai->save();
        }

        Auth::guard('pegawai')->login($pegawai);
        $request->session()->regenerate();

        return redirect()->intended('/pegawai/home');
    }

    public function logout(Request $request)
    {
        Auth::guard('pegawai')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('pegawai.login')->with('status', 'Anda berhasil logout dari akun pegawai.');
    }
}
