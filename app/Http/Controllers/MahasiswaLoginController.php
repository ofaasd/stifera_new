<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MahasiswaLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('mahasiswa')->check()) {
            return redirect()->route('mahasiswa.home');
        }

        $CurrentPage = 'page-login';

        return view('auth.mahasiswa-login', compact('CurrentPage'));
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ], [
            'nim.required' => 'NIM wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $mahasiswa = Mahasiswa::query()
            ->where('nim', $validated['nim'])
            ->where('status', 1)
            ->first();

        if (!$mahasiswa) {
            return back()->withErrors([
                'nim' => 'Akun mahasiswa tidak ditemukan atau tidak aktif.',
            ])->onlyInput('nim');
        }

        $passwordInput = $validated['password'];
        $storedHash = (string) ($mahasiswa->paswd ?? '');

        $isLaravelHashValid = !empty($storedHash) && Hash::check($passwordInput, $storedHash);
        $isLegacyMd5Valid = strlen($storedHash) === 32 && ctype_xdigit($storedHash) && hash_equals(strtolower($storedHash), md5($passwordInput));

        if (!$isLaravelHashValid && !$isLegacyMd5Valid) {
            return back()->withErrors([
                'nim' => 'NIM atau password mahasiswa tidak valid.',
            ])->onlyInput('nim');
        }

        if ($isLegacyMd5Valid) {
            $mahasiswa->paswd = Hash::make($passwordInput);
            $mahasiswa->save();
        }

        Auth::guard('mahasiswa')->login($mahasiswa);
        $request->session()->regenerate();

        return redirect()->intended('/mahasiswa/home');
    }

    public function logout(Request $request)
    {
        Auth::guard('mahasiswa')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mahasiswa.login')->with('status', 'Anda berhasil logout dari akun mahasiswa.');
    }
}
