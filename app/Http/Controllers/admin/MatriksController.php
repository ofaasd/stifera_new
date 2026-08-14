<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterMataKuliah;
use App\Models\MasterCpl;
use App\Models\MasterMatriks;
use Illuminate\Support\Facades\DB;

class MatriksController extends Controller
{
    public function index()
    {
        $data['CurrentPage'] = 'content';
        $data['title'] = 'Matriks Kurikulum';

        // Ambil data CPL aktif
        $allCpl = MasterCpl::where('is_active', true)->orderBy('kode_cpl', 'asc')->get();
        $data['cplS1'] = $allCpl->where('id_prodi', 2)->values();
        $data['cplD3'] = $allCpl->where('id_prodi', 1)->values();

        // Ambil data Mata Kuliah aktif khusus untuk kurikulum OBE (kode berawalan 25)
        $allMk = MasterMataKuliah::where('is_aktif', 1)
            ->where('kode_mata_kuliah', 'like', '25%')
            ->orderBy('semester', 'asc')
            ->orderBy('nama_mata_kuliah', 'asc')
            ->get();

        // Kelompokkan MK berdasarkan program studi dan semester
        $data['mkS1Grouped'] = $allMk->where('id_program_studi', 2)->groupBy('semester');
        $data['mkD3Grouped'] = $allMk->where('id_program_studi', 1)->groupBy('semester');

        // Ambil mapping eksisting agar lebih cepat, buat associative array [id_mk][id_cpl] = true
        $mappings = MasterMatriks::all();
        $mappedData = [];
        foreach ($mappings as $map) {
            $mappedData[$map->id_matakuliah][$map->id_cpl] = true;
        }
        $data['mappedData'] = $mappedData;

        return view('admin.matriks.index', $data);
    }

    public function store(Request $request)
    {
        $mappingInput = $request->input('mapping', []);

        DB::beginTransaction();
        try {
            // Karena ini adalah mass update/sync, kita hapus dulu semua matakuliah yang ada di input request,
            // atau cukup hapus semua isi tabel, lalu insert ulang untuk kesederhanaan (karena ini matrix bulk update).
            // Namun, lebih aman kita ambil ID MK yang dikirim, delete mapping untuk ID MK tersebut, dan insert kembali.
            // Wait, jika sebuah MK tidak dicentang sama sekali, input checkbox tidak akan terkirim.
            // Solusi: truncate the table or delete all mapping before insert.

            // Delete all current mapping
            MasterMatriks::truncate();

            // Insert the new mappings
            $inserts = [];
            $now = \Carbon\Carbon::now();
            foreach ($mappingInput as $id_mk => $cpls) {
                foreach ($cpls as $id_cpl => $val) {
                    $inserts[] = [
                        'id_matakuliah' => $id_mk,
                        'id_cpl' => $id_cpl,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (count($inserts) > 0) {
                // Chunk insert if data is too big
                $chunks = array_chunk($inserts, 500);
                foreach ($chunks as $chunk) {
                    MasterMatriks::insert($chunk);
                }
            }

            DB::commit();
            return redirect()->route('matriks.index')->with('success', 'Pemetaan Matriks Kurikulum berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['Error: ' . $e->getMessage()]);
        }
    }
}
