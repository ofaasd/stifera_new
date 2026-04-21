<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class MahasiswaProfileController extends Controller
{
    public function edit()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $dosenWali = null;
        if (!empty($mahasiswa->id_dsn_wali)) {
            $dosenWali = DB::table('pegawai_biodata as pb')
                ->leftJoin('pegawai as p', 'p.id', '=', 'pb.id_pegawai')
                ->select('pb.nama_lengkap', 'pb.nidn', 'p.npp')
                ->where('pb.id_pegawai', (int) $mahasiswa->id_dsn_wali)
                ->first();
        }

        $selectedProvinsi = (string) old('provinsi', (string) ($mahasiswa->provinsi ?? ''));
        $selectedKota = (string) old('kokab', (string) ($mahasiswa->kokab ?? ''));
        $selectedKecamatan = (string) old('kecamatan', (string) ($mahasiswa->kecamatan ?? ''));

        $provinsiList = DB::table('wilayah')
            ->select('id_wil', 'nm_wil')
            ->where('id_induk_wilayah', '000000')
            ->orderBy('nm_wil')
            ->get();

        $kotaList = collect();
        if ($selectedProvinsi !== '') {
            $kotaList = DB::table('wilayah')
                ->select('id_wil', 'nm_wil')
                ->where('id_induk_wilayah', $selectedProvinsi)
                ->orderBy('nm_wil')
                ->get();
        }

        $kecamatanList = collect();
        if ($selectedKota !== '') {
            $kecamatanList = DB::table('wilayah')
                ->select('id_wil', 'nm_wil')
                ->where('id_induk_wilayah', $selectedKota)
                ->orderBy('nm_wil')
                ->get();
        }

        $selectedWilayahIds = array_values(array_filter([
            $selectedProvinsi,
            $selectedKota,
            $selectedKecamatan,
        ]));

        $selectedWilayahMap = collect();
        if (!empty($selectedWilayahIds)) {
            $selectedWilayahMap = DB::table('wilayah')
                ->select('id_wil', 'nm_wil')
                ->whereIn('id_wil', $selectedWilayahIds)
                ->pluck('nm_wil', 'id_wil');
        }

        $selectedWilayahLabel = [
            'provinsi' => $selectedWilayahMap->get($selectedProvinsi),
            'kokab' => $selectedWilayahMap->get($selectedKota),
            'kecamatan' => $selectedWilayahMap->get($selectedKecamatan),
        ];

        return view('mahasiswa.profile', [
            'title' => 'Profile Mahasiswa',
            'CurrentPage' => 'content',
            'mahasiswa' => $mahasiswa,
            'dosenWali' => $dosenWali,
            'provinsiList' => $provinsiList,
            'kotaList' => $kotaList,
            'kecamatanList' => $kecamatanList,
            'selectedWilayahLabel' => $selectedWilayahLabel,
        ]);
    }

    public function update(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tgl_lahir' => ['nullable', 'date'],
            'jk' => ['nullable', 'integer', 'in:1,2'],
            'agama' => ['nullable', 'integer', 'between:1,6'],
            'nama_ibu' => ['nullable', 'string', 'max:150'],
            'nama_ayah' => ['nullable', 'string', 'max:150'],
            'hp_ortu' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'alamat_semarang' => ['nullable', 'string', 'max:255'],
            'rt' => ['nullable', 'string', 'max:10'],
            'rw' => ['nullable', 'string', 'max:10'],
            'kelurahan' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'exists:wilayah,id_wil'],
            'kokab' => ['nullable', 'string', 'exists:wilayah,id_wil'],
            'provinsi' => ['nullable', 'string', 'exists:wilayah,id_wil'],
            'telp' => ['nullable', 'string', 'max:30'],
            'hp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
        ]);

        DB::table('mahasiswa')
            ->where('id', (int) $mahasiswa->id)
            ->update($validated);

        return redirect()->route('mahasiswa.profile.edit')->with('status', 'Profile mahasiswa berhasil diperbarui.');
    }

    public function uploadPhoto(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if (!$request->hasFile('foto') || !$request->file('foto')->isValid()) {
            return redirect()->route('mahasiswa.profile.edit')->with('error', 'File foto tidak valid.');
        }

        $oldPhoto = (string) ($mahasiswa->foto_mhs ?? '');
        $safeNim = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) ($mahasiswa->nim ?? $mahasiswa->id));
        $ext = strtolower((string) $request->file('foto')->getClientOriginalExtension());
        $photoName = 'mahasiswa_' . $safeNim . '_' . time() . '.' . $ext;

        $photoDir = public_path('assets/foto_mahasiswa');
        File::ensureDirectoryExists($photoDir);

        $request->file('foto')->move($photoDir, $photoName);

        DB::table('mahasiswa')
            ->where('id', (int) $mahasiswa->id)
            ->update(['foto_mhs' => $photoName]);

        if ($oldPhoto !== '' && $oldPhoto !== $photoName) {
            $oldPath = $photoDir . DIRECTORY_SEPARATOR . $oldPhoto;
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        return redirect()->route('mahasiswa.profile.edit')->with('status', 'Foto profile berhasil diperbarui.');
    }

    public function getWilayahChildren(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            abort(403);
        }

        $parentId = (string) $request->query('parent_id', '');
        if ($parentId === '') {
            return response()->json([]);
        }

        $rows = DB::table('wilayah')
            ->select('id_wil', 'nm_wil')
            ->where('id_induk_wilayah', $parentId)
            ->orderBy('nm_wil')
            ->get();

        return response()->json($rows);
    }

    public function editPassword()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        return view('mahasiswa.ganti_password', [
            'title' => 'Ganti Password',
            'CurrentPage' => 'content',
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'confirmed',
            ],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.regex' => 'Password baru harus mengandung huruf besar, huruf kecil, dan angka.',
            'password.confirmed' => 'Konfirmasi password baru tidak sama.',
        ]);

        $storedHash = (string) ($mahasiswa->paswd ?? '');
        $inputCurrent = (string) $request->input('current_password');

        $isLaravelHashValid = !empty($storedHash) && Hash::check($inputCurrent, $storedHash);
        $isLegacyMd5Valid = strlen($storedHash) === 32 && ctype_xdigit($storedHash) && hash_equals(strtolower($storedHash), md5($inputCurrent));

        if (!$isLaravelHashValid && !$isLegacyMd5Valid) {
            return back()->withInput()->withErrors([
                'current_password' => 'Password lama tidak sesuai.',
            ]);
        }

        DB::table('mahasiswa')
            ->where('id', (int) $mahasiswa->id)
            ->update([
                'paswd' => Hash::make((string) $request->input('password')),
            ]);

        return redirect()->route('mahasiswa.password.edit')->with('status', 'Password berhasil diperbarui.');
    }
}
