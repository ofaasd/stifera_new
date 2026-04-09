<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\MasterKeuanganMh;
use App\Models\MasterTahunAjaran;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KeuanganMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjaranList = MasterTahunAjaran::orderByDesc('id_tahun')
            ->orderByDesc('id')
            ->get();

        $selectedTahunId = (int) $request->query('id_tahun_ajaran', $tahunAjaranList->first()->id ?? 0);
        $selectedProdiId = (int) $request->query('id_program_studi', 0);
        $selectedAngkatan = (int) $request->query('angkatan', 0);

        // Get the selected tahun ajaran to know its tipe_mhs
        $selectedTahun = $tahunAjaranList->firstWhere('id', $selectedTahunId);
        $selectedTipeMhs = $selectedTahun ? (int) $selectedTahun->tipe_mhs : 0;

        $programStudiList = ProgramStudi::query()
            ->orderBy('nama_jurusan')
            ->get(['id', 'nama_jurusan']);

        // Only show angkatan of mahasiswa matching the selected tahun ajaran's tipe_mhs
        $angkatanQuery = Mahasiswa::query()
            ->whereNotNull('angkatan')
            ->distinct()
            ->orderByDesc('angkatan');

        if ($selectedTipeMhs > 0) {
            $angkatanQuery->where('tipe_mhs', $selectedTipeMhs);
        }

        $angkatanList = $angkatanQuery->pluck('angkatan');

        $hasDataForSelectedYear = MasterKeuanganMh::where('id_tahun_ajaran', $selectedTahunId)->exists();

        $mahasiswaList = Mahasiswa::query()
            ->leftJoin('master_keuangan_mhs', function ($join) use ($selectedTahunId) {
                $join->on('mahasiswa.id', '=', 'master_keuangan_mhs.id_mahasiswa')
                    ->where('master_keuangan_mhs.id_tahun_ajaran', '=', $selectedTahunId);
            })
            ->select([
                'mahasiswa.id',
                'mahasiswa.nim',
                'mahasiswa.nama',
                'master_keuangan_mhs.krs',
                'master_keuangan_mhs.khs',
                'master_keuangan_mhs.uts',
                'master_keuangan_mhs.uas',
            ])
            ->orderBy('mahasiswa.nim');

        // Filter by tipe_mhs matching the selected tahun ajaran
        if ($selectedTipeMhs > 0) {
            $mahasiswaList->where('mahasiswa.tipe_mhs', $selectedTipeMhs);
        }

        if ($selectedProdiId > 0) {
            $mahasiswaList->where('mahasiswa.id_program_studi', $selectedProdiId);
        }

        if ($selectedAngkatan > 0) {
            $mahasiswaList->where('mahasiswa.angkatan', $selectedAngkatan);
        }

        if ($hasDataForSelectedYear) {
            $mahasiswaList = $mahasiswaList->get();
        } else {
            $mahasiswaList = collect();
        }

        return view('admin.keuangan.index', [
            'title' => 'Keuangan Mahasiswa',
            'CurrentPage' => 'content',
            'tahunAjaranList' => $tahunAjaranList,
            'selectedTahunId' => $selectedTahunId,
            'selectedTahun' => $selectedTahun,
            'programStudiList' => $programStudiList,
            'selectedProdiId' => $selectedProdiId,
            'angkatanList' => $angkatanList,
            'selectedAngkatan' => $selectedAngkatan,
            'hasDataForSelectedYear' => $hasDataForSelectedYear,
            'mahasiswaList' => $mahasiswaList,
            'statusOptions' => [
                0 => 'Belum Aktif',
                1 => 'Aktif',
            ],
        ]);
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id_mahasiswa' => 'required|integer|exists:mahasiswa,id',
            'id_tahun_ajaran' => 'required|integer|exists:master_tahun_ajaran,id',
            'field' => 'required|string|in:krs,khs,uts,uas',
            'value' => 'required|integer|in:0,1',
        ]);

        $data = [
            'id_mahasiswa' => (int) $validated['id_mahasiswa'],
            'id_tahun_ajaran' => (int) $validated['id_tahun_ajaran'],
        ];

        $record = MasterKeuanganMh::firstOrNew($data);

        if (!$record->exists) {
            $record->krs = 0;
            $record->uts = 0;
            $record->uas = 0;
            $record->khs = 0;
        }

        $field = $validated['field'];
        $record->{$field} = (int) $validated['value'];
        $record->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status ' . strtoupper($field) . ' berhasil diperbarui.',
            ]);
        }

        return redirect()
            ->to(url('master/keuangan?id_tahun_ajaran=' . $validated['id_tahun_ajaran']))
            ->with('status', 'Status ' . strtoupper($field) . ' berhasil diperbarui.');
    }

    public function generateData(Request $request)
    {
        $validated = $request->validate([
            'id_tahun_ajaran' => 'required|integer|exists:master_tahun_ajaran,id',
        ]);

        $idTahunAjaran = (int) $validated['id_tahun_ajaran'];

        $tahunAjaran = MasterTahunAjaran::find($idTahunAjaran);
        $tipeMhs = $tahunAjaran ? (int) $tahunAjaran->tipe_mhs : null;

        DB::table('master_keuangan_mhs')->insertUsing(
            ['id_mahasiswa', 'id_tahun_ajaran', 'krs', 'khs', 'uts', 'uas'],
            DB::table('mahasiswa as m')
                ->selectRaw('m.id as id_mahasiswa, ? as id_tahun_ajaran, 0 as krs, 0 as khs, 0 as uts, 0 as uas', [$idTahunAjaran])
                ->when($tipeMhs, fn ($q) => $q->where('m.tipe_mhs', $tipeMhs))
                ->whereNotExists(function ($query) use ($idTahunAjaran) {
                    $query->select(DB::raw(1))
                        ->from('master_keuangan_mhs as km')
                        ->whereColumn('km.id_mahasiswa', 'm.id')
                        ->where('km.id_tahun_ajaran', $idTahunAjaran);
                })
        );

        return redirect()
            ->to(url('master/keuangan?id_tahun_ajaran=' . $idTahunAjaran))
            ->with('status', 'Generate data keuangan mahasiswa berhasil. Silakan lanjut ubah status KRS/UTS/UAS.');
    }

    public function detectCurrentYear()
    {
        $tahunAktifList = MasterTahunAjaran::where('is_aktif', 1)
            ->orderByDesc('id')
            ->get();

        if ($tahunAktifList->isEmpty()) {
            return redirect()
                ->to(url('master/keuangan'))
                ->with('error', 'Tahun ajaran aktif tidak ditemukan.');
        }

        $messages = [];
        foreach ($tahunAktifList as $tahunAktif) {
            $tipeMhsLabel = $tahunAktif->tipe_mhs == 1 ? 'Reguler' : 'RPL';
            $totalMahasiswa = Mahasiswa::where('tipe_mhs', $tahunAktif->tipe_mhs)->count();
            $totalDataKeuangan = MasterKeuanganMh::where('id_tahun_ajaran', $tahunAktif->id)
                ->distinct('id_mahasiswa')
                ->count('id_mahasiswa');
            $belumTersedia = max(0, $totalMahasiswa - $totalDataKeuangan);
            $messages[] = '[' . $tipeMhsLabel . '] ' . $tahunAktif->awal . '/' . $tahunAktif->akhir
                . ': Tersedia ' . $totalDataKeuangan . ' dari ' . $totalMahasiswa . ' mhs, Belum: ' . $belumTersedia;
        }

        return redirect()
            ->to(url('master/keuangan?id_tahun_ajaran=' . $tahunAktifList->first()->id))
            ->with('status', 'Deteksi selesai. ' . implode(' | ', $messages) . '.');
    }

    public function resetStatus(Request $request)
    {
        $validated = $request->validate([
            'id_tahun_ajaran' => 'required|integer|exists:master_tahun_ajaran,id',
        ]);

        $idTahunAjaran = (int) $validated['id_tahun_ajaran'];

        $tahunAjaran = MasterTahunAjaran::find($idTahunAjaran);
        $tipeMhs = $tahunAjaran ? (int) $tahunAjaran->tipe_mhs : null;

        DB::transaction(function () use ($idTahunAjaran, $tipeMhs) {
            MasterKeuanganMh::where('id_tahun_ajaran', $idTahunAjaran)
                ->update([
                    'krs' => 1,
                    'khs' => 1,
                    'uts' => 1,
                    'uas' => 1,
                ]);

            DB::table('master_keuangan_mhs')->insertUsing(
                ['id_mahasiswa', 'id_tahun_ajaran', 'krs', 'khs', 'uts', 'uas'],
                DB::table('mahasiswa as m')
                    ->selectRaw('m.id as id_mahasiswa, ? as id_tahun_ajaran, 1 as krs, 0 as khs, 1 as uts, 1 as uas', [$idTahunAjaran])
                    ->when($tipeMhs, fn ($q) => $q->where('m.tipe_mhs', $tipeMhs))
                    ->whereNotExists(function ($query) use ($idTahunAjaran) {
                        $query->select(DB::raw(1))
                            ->from('master_keuangan_mhs as km')
                            ->whereColumn('km.id_mahasiswa', 'm.id')
                            ->where('km.id_tahun_ajaran', $idTahunAjaran);
                    })
            );
        });

        return redirect()
            ->to(url('master/keuangan?id_tahun_ajaran=' . $idTahunAjaran))
            ->with('status', 'Reset berhasil. Seluruh status KRS, UTS, dan UAS diset menjadi Aktif.');
    }
}
