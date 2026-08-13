<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterCpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CplController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['title'] = 'Master Data CPL';
        $data['CurrentPage'] = 'content'; // Agar nav-header tidak undefined

        $query = MasterCpl::where('is_active', true);

        // Filter Aspek Kompetensi
        if ($request->has('filter_aspek') && $request->filter_aspek !== '') {
            $query->where('kategori_aspek', $request->filter_aspek);
        }

        $allCpl = $query->orderBy('kode_cpl', 'asc')->get();

        // Pisahkan data berdasarkan program studi
        $data['cplD3'] = $allCpl->filter(function ($cpl) {
            return $cpl->id_prodi == 1;
        })->values();

        $data['cplS1'] = $allCpl->filter(function ($cpl) {
            return $cpl->id_prodi == 2;
        })->values();

        $data['prodiList'] = DB::table('program_studi')->orderBy('id', 'asc')->get();
        $data['aspekList'] = ['Sikap', 'Pengetahuan', 'Keterampilan Umum', 'Keterampilan Khusus'];

        return view('admin.cpl.index', $data);
    }

    public function create()
    {
        // unused in modal setup
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_prodi' => 'required|integer',
            'kategori_aspek' => 'required|in:Sikap,Pengetahuan,Keterampilan Umum,Keterampilan Khusus',
            'kode_cpl' => 'required|string|max:10',
            'deskripsi' => 'required|string',
            'referensi' => 'nullable|string|max:100',
            'target_capaian' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        MasterCpl::create([
            'id_prodi' => $request->id_prodi,
            'id_kurikulum' => $request->id_kurikulum ?? null,
            'kategori_aspek' => $request->kategori_aspek,
            'kode_cpl' => $request->kode_cpl,
            'deskripsi' => $request->deskripsi,
            'referensi' => $request->referensi,
            'target_capaian' => $request->target_capaian,
            'is_active' => true,
        ]);

        return redirect('master/cpl')->with('success', 'Data CPL berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $cpl = MasterCpl::findOrFail($id);
        return response()->json($cpl);
    }

    public function update(Request $request, $id)
    {
        $cpl = MasterCpl::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'id_prodi' => 'required|integer',
            'kategori_aspek' => 'required|in:Sikap,Pengetahuan,Keterampilan Umum,Keterampilan Khusus',
            'kode_cpl' => 'required|string|max:10',
            'deskripsi' => 'required|string',
            'referensi' => 'nullable|string|max:100',
            'target_capaian' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cpl->update([
            'id_prodi' => $request->id_prodi,
            'id_kurikulum' => $request->id_kurikulum ?? null,
            'kategori_aspek' => $request->kategori_aspek,
            'kode_cpl' => $request->kode_cpl,
            'deskripsi' => $request->deskripsi,
            'referensi' => $request->referensi,
            'target_capaian' => $request->target_capaian,
        ]);

        return redirect('master/cpl')->with('success', 'Data CPL berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $cpl = MasterCpl::findOrFail($id);
        $cpl->update(['is_active' => false]);
        return redirect('master/cpl')->with('success', 'Data CPL berhasil dinonaktifkan (dihapus dari view)!');
    }
}
