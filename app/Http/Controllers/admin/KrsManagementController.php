<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterKr;
use App\Models\MasterKrsTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class KrsManagementController extends Controller
{
    public function index(Request $request)
    {
        $data['title'] = 'Management KRS';
        $data['CurrentPage'] = 'content';
        $data['activeTab'] = $request->input('active_tab', session('active_tab'));

        $traceSql = (int) $request->query('trace_sql', 0) === 1;
        $data['traceSqlEnabled'] = $traceSql;
        $debugSql = [];

        $tahunRegulerQuery = DB::table('master_tahun_ajaran')
            ->where('is_aktif', 1)
            ->where('tipe_mhs', 1)
            ->orderByDesc('id');
        $debugSql['tahun_aktif_reguler'] = $this->buildDebugSql('Tahun aktif Reguler', $tahunRegulerQuery);
        $tahunReguler = $tahunRegulerQuery->first();

        if (!$tahunReguler) {
            $tahunRegulerFallbackQuery = DB::table('master_tahun_ajaran')
                ->where('tipe_mhs', 1)
                ->orderByDesc('id');
            $debugSql['tahun_aktif_reguler_fallback'] = $this->buildDebugSql('Tahun fallback Reguler', $tahunRegulerFallbackQuery);
            $tahunReguler = $tahunRegulerFallbackQuery->first();
        }

        $tahunRplQuery = DB::table('master_tahun_ajaran')
            ->where('is_aktif', 1)
            ->where('tipe_mhs', 2)
            ->orderByDesc('id');
        $debugSql['tahun_aktif_rpl'] = $this->buildDebugSql('Tahun aktif RPL', $tahunRplQuery);
        $tahunRpl = $tahunRplQuery->first();

        if (!$tahunRpl) {
            $tahunRplFallbackQuery = DB::table('master_tahun_ajaran')
                ->where('tipe_mhs', 2)
                ->orderByDesc('id');
            $debugSql['tahun_aktif_rpl_fallback'] = $this->buildDebugSql('Tahun fallback RPL', $tahunRplFallbackQuery);
            $tahunRpl = $tahunRplFallbackQuery->first();
        }

        $data['tahunReguler'] = $tahunReguler;
        $data['tahunRpl'] = $tahunRpl;

        $mahasiswaAktifRegulerQuery = DB::table('mahasiswa')
            ->where('tipe_mhs', 1)
            ->where('status', 1);
        $debugSql['total_mahasiswa_aktif_reguler'] = $this->buildDebugSql('Total mahasiswa aktif Reguler', $mahasiswaAktifRegulerQuery, 'count(id)');
        $data['totalMahasiswaAktifReguler'] = (int) $mahasiswaAktifRegulerQuery->count('id');

        $mahasiswaAktifRplQuery = DB::table('mahasiswa')
            ->where('tipe_mhs', 2)
            ->where('status', 1);
        $debugSql['total_mahasiswa_aktif_rpl'] = $this->buildDebugSql('Total mahasiswa aktif RPL', $mahasiswaAktifRplQuery, 'count(id)');
        $data['totalMahasiswaAktifRpl'] = (int) $mahasiswaAktifRplQuery->count('id');

        $krsRegulerQuery = null;
        if ($tahunReguler) {
            $krsRegulerQuery = $this->getKrsSummaryQueryByTahun((int) $tahunReguler->id, 1);
            $debugSql['krs_list_reguler'] = $this->buildDebugSql('KRS summary Reguler', $krsRegulerQuery);
            $data['krsListReguler'] = $krsRegulerQuery->get();
        } else {
            $data['krsListReguler'] = collect();
        }

        $krsRplQuery = null;
        if ($tahunRpl) {
            $krsRplQuery = $this->getKrsSummaryQueryByTahun((int) $tahunRpl->id, 2);
            $debugSql['krs_list_rpl'] = $this->buildDebugSql('KRS summary RPL', $krsRplQuery);
            $data['krsListRpl'] = $krsRplQuery->get();
        } else {
            $data['krsListRpl'] = collect();
        }

        $data['totalMahasiswaInputKrsReguler'] = (int) $data['krsListReguler']->where('total_krs', '>', 0)->count();
        $data['totalMahasiswaInputKrsRpl'] = (int) $data['krsListRpl']->where('total_krs', '>', 0)->count();

        if ($traceSql) {
            $data['traceSqlData'] = $debugSql;
        }

        return view('admin.krs.index', $data);
    }

    public function downloadLog(string $tipe_mhs)
    {
        $tipeMhs = (int) $tipe_mhs;
        if (!in_array($tipeMhs, [1, 2], true)) {
            return redirect()->back()->with('error', 'Tipe mahasiswa tidak valid.');
        }

        $tahunAktif = $this->getTahunAktifByTipe($tipeMhs);
        if (!$tahunAktif) {
            return redirect()->back()->with('error', 'Tahun ajaran aktif belum tersedia.');
        }

        $rows = $this->getKrsSummaryByTahun((int) $tahunAktif->id, $tipeMhs);
        $jenis = $this->formatJenisSemester((int) ($tahunAktif->jenis ?? 0));
        $ta = ($tahunAktif->awal ?? '-') . ' - ' . ($tahunAktif->akhir ?? '-') . ' (' . $jenis . ')';

        $lines = [];
        $lines[] = 'No,Tahun Ajaran,NIM,Nama Mahasiswa,Jumlah KRS,Status Keuangan';
        foreach ($rows as $idx => $row) {
            $line = [
                $idx + 1,
                $ta,
                $this->escapeCsv((string) $row->nim),
                $this->escapeCsv((string) ($row->nama_mhs ?? '-')),
                $this->escapeCsv((string) (($row->total_sks ?? 0) . '/24')),
                'BELUM LUNAS',
            ];
            $lines[] = implode(',', $line);
        }

        $filename = 'log_krs_' . ($tipeMhs === 2 ? 'rpl' : 'reguler') . '_' . now()->format('Ymd_His') . '.csv';

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function updateTranskrip(Request $request, string $tipe_mhs)
    {
        $tipeMhs = (int) $tipe_mhs;
        if (!in_array($tipeMhs, [1, 2], true)) {
            return redirect()->back()->with('error', 'Tipe mahasiswa tidak valid.');
        }

        $tahunAktif = $this->getTahunAktifByTipe($tipeMhs);
        if (!$tahunAktif) {
            return redirect()->back()->with('error', 'Tahun ajaran aktif untuk tipe mahasiswa ini belum tersedia.');
        }

        $rows = MasterKrsTemp::query()
            ->where('id_tahun', (int) $tahunAktif->id)
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data KRS berjalan yang bisa dipindahkan.');
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                MasterKr::query()->updateOrCreate(
                    [
                        'id_krs' => (int) $row->id,
                        'id_tahun' => (int) $row->id_tahun,
                        'nim' => $row->nim,
                    ],
                    [
                        'id_jadwal' => $row->id_jadwal,
                        'mata_kuliah' => $row->mata_kuliah,
                        'sks' => $row->sks,
                        'hari' => $row->hari,
                        'sesi' => $row->sesi,
                        'ruang' => $row->ruang,
                        'kelas' => $row->kelas,
                        'id_dosen' => $row->id_dosen,
                        'is_publish' => $row->is_publish,
                        'log_date' => $row->log_date ?? now(),
                    ]
                );
            }

            MasterKrsTemp::query()->whereIn('id', $rows->pluck('id')->all())->delete();
        });

        $labelTipe = $tipeMhs === 2 ? 'RPL' : 'Reguler';
        $activeTab = $tipeMhs === 2 ? 'rpl-pane' : 'reguler-pane';

        return redirect('master/krs')
            ->with('status', count($rows) . ' data KRS ' . $labelTipe . ' berhasil dipindah ke KRS arsip.')
            ->with('active_tab', $activeTab);
    }

    public function showDetail(string $id_tahun, string $nim)
    {
        $idTahun = (int) $id_tahun;
        $nimClean = trim($nim);

        $mahasiswa = DB::table('mahasiswa')->where('nim', $nimClean)->first();
        if (!$mahasiswa) {
            return redirect('master/krs')->with('error', 'Mahasiswa tidak ditemukan.');
        }

        $tahun = DB::table('master_tahun_ajaran')->where('id', $idTahun)->first();
        if (!$tahun) {
            return redirect('master/krs')->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $krsRows = DB::table('master_krs_temp as mkt')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mkt.mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mkt.id_dosen')
            ->leftJoin('master_nilai as mn', function ($join) {
                $join->on('mn.nim', '=', 'mkt.nim')
                     ->on('mn.id_jadwal', '=', 'mkt.id_jadwal')
                     ->on('mn.id_tahun', '=', 'mkt.id_tahun');
            })
            ->select(
                'mkt.*',
                'mmk.nama_mata_kuliah',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen"),
                DB::raw('CASE WHEN (mn.ntugas IS NOT NULL OR mn.nuts IS NOT NULL OR mn.nuas IS NOT NULL) THEN 1 ELSE 0 END as ada_nilai'),
                'mn.ntugas',
                'mn.nuts',
                'mn.nuas'
            )
            ->where('mkt.nim', $nimClean)
            ->where('mkt.id_tahun', $idTahun)
            ->orderBy('mkt.id')
            ->get();

        $sudahDiambil = $krsRows->pluck('id_jadwal')->filter()->values()->all();

        $jadwalList = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mjt.id_dosen')
            ->select(
                'mjt.id',
                'mjt.kode_mata_kuliah',
                'mjt.hari',
                'mjt.sesi',
                'mjt.ruang',
                'mjt.rombel',
                'mjt.kelas',
                'mjt.id_dosen',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->where('mjt.id_tahun', $idTahun)
            ->where('mjt.status', 1)
            ->when(!empty($sudahDiambil), function ($query) use ($sudahDiambil) {
                $query->whereNotIn('mjt.id', $sudahDiambil);
            })
            ->orderBy('mmk.nama_mata_kuliah')
            ->get();

        $data['title'] = 'Detail KRS Mahasiswa';
        $data['CurrentPage'] = 'content';
        $data['mahasiswa'] = $mahasiswa;
        $data['tahun'] = $tahun;
        $data['krsRows'] = $krsRows;
        $data['jadwalList'] = $jadwalList;
        $data['adaNilai'] = $krsRows->contains(fn ($r) => (int) ($r->ada_nilai ?? 0) === 1);
        $data['idTahun'] = $idTahun;
        $data['jenisTA'] = $this->formatJenisSemester((int) ($tahun->jenis ?? 0));

        return view('admin.krs.detail', $data);
    }

    public function storeKrs(Request $request, string $id_tahun, string $nim)
    {
        $request->validate(['id_jadwal' => 'required|integer']);

        $idTahun = (int) $id_tahun;
        $nimClean = trim($nim);
        $idJadwal = (int) $request->id_jadwal;

        $mahasiswa = DB::table('mahasiswa')->where('nim', $nimClean)->first();
        if (!$mahasiswa) {
            return redirect('master/krs')->with('error', 'Mahasiswa tidak ditemukan.');
        }

        $tahun = DB::table('master_tahun_ajaran')->where('id', $idTahun)->first();
        if (!$tahun) {
            return redirect('master/krs')->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $jadwal = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->select('mjt.*', 'mmk.jumlah_sks')
            ->where('mjt.id', $idJadwal)
            ->where('mjt.id_tahun', $idTahun)
            ->where('mjt.status', 1)
            ->first();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak valid atau tidak aktif untuk tahun ajaran ini.');
        }

        $duplikat = DB::table('master_krs_temp')
            ->where('nim', $nimClean)
            ->where('id_tahun', $idTahun)
            ->where('id_jadwal', $idJadwal)
            ->exists();

        if ($duplikat) {
            return redirect()->back()->with('error', 'Jadwal ini sudah ada pada KRS mahasiswa.');
        }

        DB::table('master_krs_temp')->insert([
            'id_tahun' => $idTahun,
            'nim' => $nimClean,
            'id_jadwal' => $idJadwal,
            'mata_kuliah' => $jadwal->kode_mata_kuliah,
            'sks' => (int) ($jadwal->jumlah_sks ?? 0),
            'hari' => $jadwal->hari,
            'sesi' => $jadwal->sesi,
            'ruang' => $jadwal->ruang,
            'kelas' => $jadwal->kelas ?? null,
            'id_dosen' => $jadwal->id_dosen,
            'is_publish' => 1,
            'log_date' => now(),
        ]);

        return redirect('master/krs/detail/' . $idTahun . '/' . $nimClean)
            ->with('status', 'Data KRS berhasil ditambahkan.');
    }

    public function downloadPdf(string $id_tahun, string $nim)
    {
        $idTahun = (int) $id_tahun;
        $nimClean = trim($nim);

        $mahasiswa = DB::table('mahasiswa')->where('nim', $nimClean)->first();
        if (!$mahasiswa) {
            return redirect('master/krs')->with('error', 'Mahasiswa tidak ditemukan.');
        }

        $tahun = DB::table('master_tahun_ajaran')->where('id', $idTahun)->first();
        if (!$tahun) {
            return redirect('master/krs')->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $krsRows = DB::table('master_krs_temp as mkt')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mkt.mata_kuliah')
            ->select(
                'mkt.*',
                'mkt.mata_kuliah as kode_mata_kuliah',
                'mmk.nama_mata_kuliah'
            )
            ->where('mkt.nim', $nimClean)
            ->where('mkt.id_tahun', $idTahun)
            ->orderBy('mkt.id')
            ->get();

        if ($krsRows->isEmpty()) {
            return redirect('master/krs/detail/' . $idTahun . '/' . $nimClean)
                ->with('error', 'Data KRS belum tersedia untuk diunduh.');
        }

        $mahasiswaProfil = $this->getMahasiswaProfil((int) ($mahasiswa->id ?? 0));
        $ipsTerakhir = 0.0;
        $batasSks = function_exists('sksbatas') ? (int) sksbatas($ipsTerakhir) : 24;

        $html = view('mahasiswa.krs_pdf', [
            'mahasiswa' => $mahasiswa,
            'mahasiswaProfil' => $mahasiswaProfil,
            'tahunAktif' => $tahun,
            'jenisTA' => $this->formatJenisSemester((int) ($tahun->jenis ?? 0)),
            'krsRows' => $krsRows,
            'totalSks' => (int) $krsRows->sum('sks'),
            'ipsTerakhir' => $ipsTerakhir,
            'batasSks' => $batasSks,
        ])->render();

        $mpdf = new Mpdf([
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        $mpdf->WriteHTML($html);
        $filename = 'admin_krs_' . ($mahasiswa->nim ?? 'mahasiswa') . '_' . $idTahun . '.pdf';

        return response($mpdf->Output($filename, 'D'))
            ->header('Content-Type', 'application/pdf');
    }

    public function hapusKrs(Request $request, string $id)
    {
        $krs = DB::table('master_krs_temp')->where('id', (int) $id)->first();
        if (!$krs) {
            return redirect()->back()->with('error', 'Data KRS tidak ditemukan.');
        }

        $nilaiAda = DB::table('master_nilai')
            ->where('nim', $krs->nim)
            ->where('id_jadwal', $krs->id_jadwal)
            ->where('id_tahun', $krs->id_tahun)
            ->where(function ($q) {
                $q->whereNotNull('ntugas')
                  ->orWhereNotNull('nuts')
                  ->orWhereNotNull('nuas');
            })
            ->exists();

        if ($nilaiAda) {
            return redirect()->back()->with('error', 'KRS tidak dapat dihapus karena sudah ada nilai yang dimasukkan untuk mata kuliah ini.');
        }

        DB::table('master_krs_temp')->where('id', (int) $id)->delete();

        return redirect()->back()->with('status', 'Data KRS berhasil dihapus.');
    }

    public function editKrs(string $id)
    {
        $krs = DB::table('master_krs_temp')->where('id', (int) $id)->first();
        if (!$krs) {
            return redirect('master/krs')->with('error', 'Data KRS tidak ditemukan.');
        }

        $nilaiAda = DB::table('master_nilai')
            ->where('nim', $krs->nim)
            ->where('id_jadwal', $krs->id_jadwal)
            ->where('id_tahun', $krs->id_tahun)
            ->where(function ($q) {
                $q->whereNotNull('ntugas')
                  ->orWhereNotNull('nuts')
                  ->orWhereNotNull('nuas');
            })
            ->exists();

        if ($nilaiAda) {
            return redirect('master/krs/detail/' . $krs->id_tahun . '/' . $krs->nim)
                ->with('error', 'KRS tidak dapat diubah karena sudah ada nilai yang dimasukkan untuk mata kuliah ini.');
        }

        $mahasiswa = DB::table('mahasiswa')->where('nim', $krs->nim)->first();
        $tahun = DB::table('master_tahun_ajaran')->where('id', $krs->id_tahun)->first();

        $sudahDiambil = DB::table('master_krs_temp')
            ->where('nim', $krs->nim)
            ->where('id_tahun', $krs->id_tahun)
            ->where('id', '!=', $krs->id)
            ->pluck('id_jadwal')
            ->toArray();

        $jadwalList = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mjt.id_dosen')
            ->select(
                'mjt.id',
                'mjt.kode_mata_kuliah',
                'mjt.hari',
                'mjt.sesi',
                'mjt.ruang',
                'mjt.rombel',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->where('mjt.id_tahun', $krs->id_tahun)
            ->where('mjt.status', 1)
            ->whereNotIn('mjt.id', $sudahDiambil)
            ->orderBy('mmk.nama_mata_kuliah')
            ->get();

        $data['title'] = 'Edit KRS Mahasiswa';
        $data['CurrentPage'] = 'content';
        $data['krs'] = $krs;
        $data['mahasiswa'] = $mahasiswa;
        $data['tahun'] = $tahun;
        $data['jadwalList'] = $jadwalList;
        $data['jenisTA'] = $this->formatJenisSemester((int) ($tahun->jenis ?? 0));

        return view('admin.krs.edit', $data);
    }

    public function updateKrs(Request $request, string $id)
    {
        $request->validate(['id_jadwal' => 'required|integer']);

        $krs = DB::table('master_krs_temp')->where('id', (int) $id)->first();
        if (!$krs) {
            return redirect('master/krs')->with('error', 'Data KRS tidak ditemukan.');
        }

        $nilaiAda = DB::table('master_nilai')
            ->where('nim', $krs->nim)
            ->where('id_jadwal', $krs->id_jadwal)
            ->where('id_tahun', $krs->id_tahun)
            ->where(function ($q) {
                $q->whereNotNull('ntugas')
                  ->orWhereNotNull('nuts')
                  ->orWhereNotNull('nuas');
            })
            ->exists();

        if ($nilaiAda) {
            return redirect()->back()->with('error', 'KRS tidak dapat diubah karena sudah ada nilai yang dimasukkan.');
        }

        $newId = (int) $request->id_jadwal;
        $jadwal = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->select('mjt.*', 'mmk.nama_mata_kuliah', 'mmk.jumlah_sks')
            ->where('mjt.id', $newId)
            ->first();

        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $duplikat = DB::table('master_krs_temp')
            ->where('nim', $krs->nim)
            ->where('id_jadwal', $newId)
            ->where('id_tahun', $krs->id_tahun)
            ->where('id', '!=', $krs->id)
            ->exists();

        if ($duplikat) {
            return redirect()->back()->with('error', 'Jadwal ini sudah diambil oleh mahasiswa tersebut.');
        }

        DB::table('master_krs_temp')->where('id', (int) $id)->update([
            'id_jadwal'  => $newId,
            'mata_kuliah' => $jadwal->kode_mata_kuliah,
            'sks'        => $jadwal->jumlah_sks,
            'hari'       => $jadwal->hari,
            'sesi'       => $jadwal->sesi,
            'ruang'      => $jadwal->ruang,
            'id_dosen'   => $jadwal->id_dosen,
            'kelas'      => $jadwal->kelas ?? null,
            'log_date'   => now(),
        ]);

        return redirect('master/krs/detail/' . $krs->id_tahun . '/' . $krs->nim)
            ->with('status', 'Data KRS berhasil diperbarui.');
    }

    private function getTahunAktifByTipe(int $tipeMhs): ?object
    {
        $tahun = DB::table('master_tahun_ajaran')
            ->where('is_aktif', 1)
            ->where('tipe_mhs', $tipeMhs)
            ->orderByDesc('id')
            ->first();

        if (!$tahun) {
            $tahun = DB::table('master_tahun_ajaran')
                ->where('tipe_mhs', $tipeMhs)
                ->orderByDesc('id')
                ->first();
        }

        return $tahun;
    }

    private function getTotalMahasiswaAktifByTipe(int $tipeMhs): int
    {
        return (int) DB::table('mahasiswa')
            ->where('tipe_mhs', $tipeMhs)
            ->where('status', 1)
            ->count('id');
    }

    private function getKrsSummaryByTahun(int $idTahun, int $tipeMhs)
    {
        return $this->getKrsSummaryQueryByTahun($idTahun, $tipeMhs)->get();
    }

    private function getKrsSummaryQueryByTahun(int $idTahun, int $tipeMhs)
    {
        return DB::table('mahasiswa as m')
            ->leftJoin('master_krs_temp as mkt', function ($join) use ($idTahun) {
                $join->on('mkt.nim', '=', 'm.nim')
                    ->where('mkt.id_tahun', '=', $idTahun);
            })
            ->select(
                'm.nim',
                'm.nama as nama_mhs',
                DB::raw('SUM(COALESCE(mkt.sks, 0)) as total_sks'),
                DB::raw('COUNT(mkt.id) as total_krs')
            )
            ->where('m.tipe_mhs', $tipeMhs)
            ->where('m.status', 1)
            ->groupBy('m.nim', 'm.nama')
            ->orderBy('m.nim')
            ->limit(500);
    }

    private function formatJenisSemester(int $jenis): string
    {
        if ($jenis === 1) {
            return 'Ganjil';
        }
        if ($jenis === 2) {
            return 'Genap';
        }
        if ($jenis === 3) {
            return 'Antara Ganjil Genap';
        }
        if ($jenis === 4) {
            return 'Antara Genap Ganjil';
        }

        return '-';
    }

    private function escapeCsv(string $value): string
    {
        $escaped = str_replace('"', '""', $value);
        return '"' . $escaped . '"';
    }

    private function getMahasiswaProfil(int $idMahasiswa): ?object
    {
        return DB::table('mahasiswa as m')
            ->leftJoin('program_studi as ps', 'ps.id', '=', 'm.id_program_studi')
            ->leftJoin('master_program_studi as mps', 'mps.id', '=', 'm.id_program_studi')
            ->leftJoin('pegawai_biodata as pbw', 'pbw.id_pegawai', '=', 'm.id_dsn_wali')
            ->select(
                'm.id',
                'm.nim',
                'm.nama',
                DB::raw("COALESCE(NULLIF(CONCAT(COALESCE(ps.jenjang,''), ' / ', COALESCE(ps.nama_jurusan,'')), ' / '), mps.nama_jurusan, '-') as nama_program_studi"),
                DB::raw("CONCAT(COALESCE(pbw.gelar_depan,''), ' ', COALESCE(pbw.nama_lengkap,''), ' ', COALESCE(pbw.gelar_belakang,'')) as dosen_wali")
            )
            ->where('m.id', $idMahasiswa)
            ->first();
    }

    private function buildDebugSql(string $label, $query, ?string $aggregate = null): array
    {
        $sql = $aggregate ? $query->clone()->selectRaw($aggregate)->toSql() : $query->toSql();
        $bindings = $query->getBindings();

        return [
            'label' => $label,
            'sql' => $sql,
            'bindings' => $bindings,
            'final_sql' => $this->interpolateQuery($sql, $bindings),
        ];
    }

    private function interpolateQuery(string $sql, array $bindings): string
    {
        $index = 0;

        return preg_replace_callback('/\?/', function () use ($bindings, &$index) {
            if (!array_key_exists($index, $bindings)) {
                return '?';
            }

            $binding = $bindings[$index++];

            if ($binding === null) {
                return 'NULL';
            }

            if (is_bool($binding)) {
                return $binding ? '1' : '0';
            }

            if (is_numeric($binding)) {
                return (string) $binding;
            }

            return "'" . str_replace("'", "''", (string) $binding) . "'";
        }, $sql) ?? $sql;
    }
}
