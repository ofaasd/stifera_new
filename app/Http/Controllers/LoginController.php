<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index() {
        $CurrentPage = 'page-login';
        return view('page-login', compact('CurrentPage'));
    }
    public function authenticate(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Cek Kredensial & Buat Session
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        // 3. Gagal Login
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    public function logout(Request $request)
    {
        // 1. Keluarkan user dari autentikasi Laravel
        Auth::logout();

        // 2. Batalkan session yang sedang berjalan saat ini
        $request->session()->invalidate();

        // 3. Generate ulang token CSRF untuk keamanan form berikutnya
        $request->session()->regenerateToken();

        // 4. Arahkan kembali ke halaman login atau halaman depan
        return redirect('/login')->with('status', 'Anda berhasil logout.');
    }
}
