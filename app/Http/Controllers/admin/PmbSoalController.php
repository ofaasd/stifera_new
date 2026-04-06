<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TblKunci;
use App\Models\TblPilihanSoal;
use App\Models\TblSoalPmb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PmbSoalController extends Controller
{
    public function index()
    {
        $data['title'] = 'Daftar Soal PMB';
        $data['CurrentPage'] = 'content';
        $data['soalList'] = TblSoalPmb::with('kunciJawaban')
            ->withCount('pilihanSoal')
            ->orderByDesc('id')
            ->get();

        return view('admin.pmb_soal.index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Soal PMB';
        $data['CurrentPage'] = 'content';

        return view('admin.pmb_soal.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'soal' => 'required|string',
            'is_aktif' => 'required|in:0,1',
            'pilihan' => 'required|array|min:1',
            'pilihan.*' => 'required|string',
            'kunci_index' => 'required|integer|min:0',
        ], [
            'pilihan.required' => 'Pilihan jawaban wajib diisi minimal 1.',
            'pilihan.min' => 'Pilihan jawaban wajib diisi minimal 1.',
            'pilihan.*.required' => 'Setiap pilihan jawaban wajib diisi.',
            'kunci_index.required' => 'Kunci jawaban wajib dipilih.',
        ]);

        $pilihanValues = array_values($validated['pilihan']);
        $kunciIndex = (int) $validated['kunci_index'];
        if (!isset($pilihanValues[$kunciIndex])) {
            return back()
                ->withErrors(['kunci_index' => 'Kunci jawaban tidak valid.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated, $pilihanValues, $kunciIndex) {
            $soal = TblSoalPmb::create([
                'soal' => $validated['soal'],
                'is_aktif' => (int) $validated['is_aktif'],
                'by_excel' => 0,
            ]);

            $rows = [];
            foreach ($pilihanValues as $index => $pilihan) {
                $rows[] = [
                    'id_soal' => $soal->id,
                    'huruf' => chr(65 + $index),
                    'pilihan' => $pilihan,
                ];
            }

            TblPilihanSoal::insert($rows);

            TblKunci::create([
                'id_soal' => $soal->id,
                'kunci' => chr(65 + $kunciIndex),
            ]);
        });

        return redirect('pmb/soal')->with('status', 'Soal PMB berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $soal = TblSoalPmb::with(['pilihanSoal', 'kunciJawaban'])->find($id);
        if (!$soal) {
            return redirect('pmb/soal')->with('error', 'Data soal tidak ditemukan.');
        }

        $data['title'] = 'Edit Soal PMB';
        $data['CurrentPage'] = 'content';
        $data['d'] = $soal;

        return view('admin.pmb_soal.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $soal = TblSoalPmb::find($id);
        if (!$soal) {
            return redirect('pmb/soal')->with('error', 'Data soal tidak ditemukan.');
        }

        $validated = $request->validate([
            'soal' => 'required|string',
            'is_aktif' => 'required|in:0,1',
            'pilihan' => 'required|array|min:1',
            'pilihan.*' => 'required|string',
            'kunci_index' => 'required|integer|min:0',
        ], [
            'pilihan.required' => 'Pilihan jawaban wajib diisi minimal 1.',
            'pilihan.min' => 'Pilihan jawaban wajib diisi minimal 1.',
            'pilihan.*.required' => 'Setiap pilihan jawaban wajib diisi.',
            'kunci_index.required' => 'Kunci jawaban wajib dipilih.',
        ]);

        $pilihanValues = array_values($validated['pilihan']);
        $kunciIndex = (int) $validated['kunci_index'];
        if (!isset($pilihanValues[$kunciIndex])) {
            return back()
                ->withErrors(['kunci_index' => 'Kunci jawaban tidak valid.'])
                ->withInput();
        }

        DB::transaction(function () use ($soal, $validated, $pilihanValues, $kunciIndex) {
            $soal->update([
                'soal' => $validated['soal'],
                'is_aktif' => (int) $validated['is_aktif'],
            ]);

            TblPilihanSoal::where('id_soal', $soal->id)->delete();

            $rows = [];
            foreach ($pilihanValues as $index => $pilihan) {
                $rows[] = [
                    'id_soal' => $soal->id,
                    'huruf' => chr(65 + $index),
                    'pilihan' => $pilihan,
                ];
            }

            TblPilihanSoal::insert($rows);

            TblKunci::updateOrCreate(
                ['id_soal' => $soal->id],
                ['kunci' => chr(65 + $kunciIndex)]
            );
        });

        return redirect('pmb/soal')->with('status', 'Soal PMB berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $soal = TblSoalPmb::find($id);
        if (!$soal) {
            return redirect('pmb/soal')->with('error', 'Data soal tidak ditemukan.');
        }

        DB::transaction(function () use ($soal) {
            TblPilihanSoal::where('id_soal', $soal->id)->delete();
            TblKunci::where('id_soal', $soal->id)->delete();
            $soal->delete();
        });

        return redirect('pmb/soal')->with('status', 'Soal PMB berhasil dihapus.');
    }
}
