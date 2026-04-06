<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingUserController extends Controller
{
    public function index()
    {
        $data['title'] = 'Setting User';
        $data['CurrentPage'] = 'content';
        $data['users'] = User::orderByDesc('id')->get();

        return view('admin.setting_user.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah User';
        $data['CurrentPage'] = 'content';

        return view('admin.setting_user.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
        ]);

        return redirect('SettingUser')->with('status', 'User berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect('SettingUser')->with('error', 'User tidak ditemukan.');
        }

        $data['title'] = 'Edit User';
        $data['CurrentPage'] = 'content';
        $data['d'] = $user;

        return view('admin.setting_user.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect('SettingUser')->with('error', 'User tidak ditemukan.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
        ]);

        return redirect('SettingUser')->with('status', 'Data user berhasil diperbarui.');
    }

    public function editPassword(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect('SettingUser')->with('error', 'User tidak ditemukan.');
        }

        $data['title'] = 'Update Password User';
        $data['CurrentPage'] = 'content';
        $data['d'] = $user;

        return view('admin.setting_user.password', $data);
    }

    public function updatePassword(Request $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect('SettingUser')->with('error', 'User tidak ditemukan.');
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect('SettingUser')->with('status', 'Password user berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect('SettingUser')->with('error', 'User tidak ditemukan.');
        }

        $user->delete();

        return redirect('SettingUser')->with('status', 'User berhasil dihapus.');
    }
}
