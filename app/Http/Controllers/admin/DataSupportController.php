<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\MasterTahunAjaran;
use App\Models\ProgramStudi;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DataSupportController extends Controller
{
    /**
     * Halaman utama Data Support - menampilkan list sub menu
     */
    public function index()
    {
        $CurrentPage = 'content';
        $title = 'Data Support Download';

        // Sub-menu data (hardcoded untuk test)
        $menus2 = [
            [
                'id' => 'master-mahasiswa',
                'title' => 'Master Mahasiswa',
                'icon' => 'fa-solid fa-users',
                'description' => 'Download data master mahasiswa',
                'url' => '/data/master-mahasiswa',
            ],
            [
                'id' => 'ips-mahasiswa',
                'title' => 'IPS Mahasiswa',
                'icon' => 'fa-solid fa-chart-line',
                'description' => 'Download data IPS mahasiswa',
                'url' => '/data/ips-mahasiswa',
            ],
            [
                'id' => 'master-pegawai',
                'title' => 'Master Pegawai',
                'icon' => 'fa-solid fa-users-gear',
                'description' => 'Download data master pegawai',
                'url' => '/data/master-pegawai',
            ],
            [
                'id' => 'krs-per-ta',
                'title' => 'KRS Per TA',
                'icon' => 'fa-solid fa-file-spreadsheet',
                'description' => 'Download data KRS per tahun ajaran',
                'url' => '/data/krs-per-ta',
            ],
        ];

        return view('admin.data-support.index', compact('CurrentPage', 'title', 'menus2'));
    }

    /**
     * Halaman Master Mahasiswa
     */
    public function masterMahasiswa()
    {
        $CurrentPage = 'content';
        $title = 'Master Mahasiswa';
        $backUrl = '/data';

        $statusOptions = [
            1 => 'Aktif',
            2 => 'Cuti',
            3 => 'Keluar',
            4 => 'Lulus',
            5 => 'Meninggal',
            6 => 'DO',
        ];

        $angkatanList = Mahasiswa::query()
            ->whereNotNull('angkatan')
            ->distinct()
            ->orderByDesc('angkatan')
            ->pluck('angkatan');

        $programStudiList = ProgramStudi::query()
            ->orderBy('nama_jurusan')
            ->get(['id', 'nama_jurusan']);

        $selectedAngkatan = (int) request('angkatan', 0);
        $selectedStatus = (int) request('status', 0);
        $selectedProgramStudi = (int) request('id_program_studi', 0);
        $isPreview = request()->boolean('preview');

        $tableColumns = [];
        $rows = collect();

        if ($isPreview) {
            $tableColumns = Schema::getColumnListing('mahasiswa');
            $rows = $this->buildMasterMahasiswaQuery(
                $selectedAngkatan,
                $selectedStatus,
                $selectedProgramStudi
            )->get();
        }

        return view('admin.data-support.master-mahasiswa', compact(
            'CurrentPage',
            'title',
            'backUrl',
            'statusOptions',
            'angkatanList',
            'programStudiList',
            'selectedAngkatan',
            'selectedStatus',
            'selectedProgramStudi',
            'isPreview',
            'tableColumns',
            'rows'
        ));
    }

    /**
     * Export Master Mahasiswa ke Excel
     */
    public function exportMasterMahasiswaExcel(Request $request)
    {
        $selectedAngkatan = (int) $request->query('angkatan', 0);
        $selectedStatus = (int) $request->query('status', 0);
        $selectedProgramStudi = (int) $request->query('id_program_studi', 0);

        $tableColumns = Schema::getColumnListing('mahasiswa');
        $headers = array_merge($tableColumns, ['nama_program_studi', 'nama_dosen_wali']);

        $rows = $this->buildMasterMahasiswaQuery(
            $selectedAngkatan,
            $selectedStatus,
            $selectedProgramStudi
        )->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Master Mahasiswa');

        foreach ($headers as $index => $header) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $rowNumber = 2;
        foreach ($rows as $row) {
            foreach ($headers as $index => $columnName) {
                $column = Coordinate::stringFromColumnIndex($index + 1);
                $value = $row->{$columnName} ?? '';
                $sheet->setCellValue($column . $rowNumber, (string) $value);
            }
            $rowNumber++;
        }

        $fileName = 'master-mahasiswa-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function buildMasterMahasiswaQuery(int $selectedAngkatan, int $selectedStatus, int $selectedProgramStudi)
    {
        $query = DB::table('mahasiswa')
            ->leftJoin('program_studi', 'program_studi.id', '=', 'mahasiswa.id_program_studi')
            ->leftJoin('pegawai_biodata', 'pegawai_biodata.id_pegawai', '=', 'mahasiswa.id_dsn_wali')
            ->select([
                'mahasiswa.*',
                DB::raw("COALESCE(program_studi.nama_jurusan, '') as nama_program_studi"),
                DB::raw("COALESCE(pegawai_biodata.nama_lengkap, '') as nama_dosen_wali"),
            ])
            ->orderBy('mahasiswa.nim');

        if ($selectedAngkatan > 0) {
            $query->where('mahasiswa.angkatan', $selectedAngkatan);
        }

        if ($selectedStatus > 0) {
            $query->where('mahasiswa.status', $selectedStatus);
        }

        if ($selectedProgramStudi > 0) {
            $query->where('mahasiswa.id_program_studi', $selectedProgramStudi);
        }

        return $query;
    }

    /**
     * Halaman IPS Mahasiswa
     */
    public function ipsMahasiswa()
    {
        $CurrentPage = 'content';
        $title = 'IPS Mahasiswa';
        $backUrl = '/data';

        $angkatanList = Mahasiswa::query()
            ->whereNotNull('angkatan')
            ->distinct()
            ->orderByDesc('angkatan')
            ->pluck('angkatan');

        $programStudiList = ProgramStudi::query()
            ->orderBy('nama_jurusan')
            ->get(['id', 'nama_jurusan']);

        $tahunAjaranList = MasterTahunAjaran::query()
            ->orderByDesc('id_tahun')
            ->orderByDesc('id')
            ->get();

        $selectedAngkatan = (int) request('angkatan', 0);
        $selectedProgramStudi = (int) request('id_program_studi', 0);
        $selectedTahunAjaran = (int) request('id_tahun_ajaran', (int) ($tahunAjaranList->first()->id ?? 0));
        $isPreview = request()->boolean('preview');

        $rows = collect();
        if ($isPreview) {
            $rows = $this->buildIpsMahasiswaQuery(
                $selectedAngkatan,
                $selectedProgramStudi,
                $selectedTahunAjaran
            )->get();
        }

        return view('admin.data-support.ips-mahasiswa', compact(
            'CurrentPage',
            'title',
            'backUrl',
            'angkatanList',
            'programStudiList',
            'tahunAjaranList',
            'selectedAngkatan',
            'selectedProgramStudi',
            'selectedTahunAjaran',
            'isPreview',
            'rows'
        ));
    }

    private function buildIpsMahasiswaQuery(int $selectedAngkatan, int $selectedProgramStudi, int $selectedTahunAjaran)
    {
        $query = DB::table('mahasiswa as m')
            ->leftJoin('rekap_ips as ri', function ($join) use ($selectedTahunAjaran) {
                $join->on('ri.id_mhs', '=', 'm.nim');

                if ($selectedTahunAjaran > 0) {
                    $join->where('ri.id_ta', '=', $selectedTahunAjaran);
                }
            })
            ->select([
                'm.nim',
                'm.nama',
                DB::raw('COALESCE(MAX(CAST(ri.ips AS DECIMAL(8,2))), 0) as ips'),
            ])
            ->groupBy('m.id', 'm.nim', 'm.nama')
            ->orderBy('m.nim');

        if ($selectedAngkatan > 0) {
            $query->where('m.angkatan', $selectedAngkatan);
        }

        if ($selectedProgramStudi > 0) {
            $query->where('m.id_program_studi', $selectedProgramStudi);
        }

        return $query;
    }

    /**
     * Halaman Master Pegawai
     */
    public function masterPegawai()
    {
        $CurrentPage = 'content';
        $title = 'Master Pegawai';
        $backUrl = '/data';

        $posisiList = DB::table('pegawai_posisi')
            ->orderBy('nama')
            ->get(['kode', 'nama']);

        $jabatanFungsionalList = DB::table('jabatan_fungsional')
            ->orderBy('jabatan')
            ->get(['id', 'jabatan']);

        $jenisKelaminList = [
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
        ];

        $jenjangList = DB::table('program_studi')
            ->whereNotNull('jenjang')
            ->where('jenjang', '!=', '')
            ->distinct()
            ->orderBy('jenjang')
            ->pluck('jenjang');

        $programStudiList = ProgramStudi::query()
            ->orderBy('nama_jurusan')
            ->get(['id', 'nama_jurusan']);

        $statusList = DB::table('pegawai_biodata')
            ->whereNotNull('status_pegawai')
            ->where('status_pegawai', '!=', '')
            ->distinct()
            ->orderBy('status_pegawai')
            ->pluck('status_pegawai');

        $selectedPosisi = (string) request('kd_posisi_pegawai', '');
        $selectedJabatanFungsional = (string) request('jabatan_fungsional', '');
        $selectedJenisKelamin = (string) request('jenis_kelamin', '');
        $selectedJenjang = (string) request('jenjang', '');
        $selectedProgramStudi = (int) request('id_progdi', 0);
        $selectedStatus = (string) request('status_pegawai', '');
        $isPreview = request()->boolean('preview');

        $tableColumns = Schema::getColumnListing('pegawai_biodata');
        $rows = collect();

        if ($isPreview) {
            $rows = $this->buildMasterPegawaiQuery(
                $selectedPosisi,
                $selectedJabatanFungsional,
                $selectedJenisKelamin,
                $selectedJenjang,
                $selectedProgramStudi,
                $selectedStatus
            )->get();
        }

        return view('admin.data-support.master-pegawai', compact(
            'CurrentPage',
            'title',
            'backUrl',
            'posisiList',
            'jabatanFungsionalList',
            'jenisKelaminList',
            'jenjangList',
            'programStudiList',
            'statusList',
            'selectedPosisi',
            'selectedJabatanFungsional',
            'selectedJenisKelamin',
            'selectedJenjang',
            'selectedProgramStudi',
            'selectedStatus',
            'isPreview',
            'tableColumns',
            'rows'
        ));
    }

    public function exportMasterPegawaiExcel(Request $request)
    {
        $selectedPosisi = (string) $request->query('kd_posisi_pegawai', '');
        $selectedJabatanFungsional = (string) $request->query('jabatan_fungsional', '');
        $selectedJenisKelamin = (string) $request->query('jenis_kelamin', '');
        $selectedJenjang = (string) $request->query('jenjang', '');
        $selectedProgramStudi = (int) $request->query('id_progdi', 0);
        $selectedStatus = (string) $request->query('status_pegawai', '');

        $tableColumns = Schema::getColumnListing('pegawai_biodata');
        $headers = array_merge($tableColumns, [
            'nama_pegawai',
            'nama_posisi_pegawai',
            'nama_jenis_pegawai',
            'nama_jabatan_fungsional',
            'nama_jenjang',
            'nama_program_studi',
        ]);

        $rows = $this->buildMasterPegawaiQuery(
            $selectedPosisi,
            $selectedJabatanFungsional,
            $selectedJenisKelamin,
            $selectedJenjang,
            $selectedProgramStudi,
            $selectedStatus
        )->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Master Pegawai');

        foreach ($headers as $index => $header) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $rowNumber = 2;
        foreach ($rows as $row) {
            foreach ($headers as $index => $columnName) {
                $column = Coordinate::stringFromColumnIndex($index + 1);
                $value = $row->{$columnName} ?? '';
                $sheet->setCellValue($column . $rowNumber, (string) $value);
            }
            $rowNumber++;
        }

        $fileName = 'master-pegawai-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function buildMasterPegawaiQuery(
        string $selectedPosisi,
        string $selectedJabatanFungsional,
        string $selectedJenisKelamin,
        string $selectedJenjang,
        int $selectedProgramStudi,
        string $selectedStatus
    ) {
        $latestJabatanSub = DB::table('pegawai_jabatan_fungsional as pjf')
            ->selectRaw('pjf.id_pegawai, MAX(pjf.id) as last_id')
            ->groupBy('pjf.id_pegawai');

        $query = DB::table('pegawai_biodata as pb')
            ->leftJoin('pegawai as p', 'p.id', '=', 'pb.id_pegawai')
            ->leftJoin('pegawai_posisi as pp', 'pp.kode', '=', 'pb.kd_posisi_pegawai')
            ->leftJoin('pegawai_jenis as pj', 'pj.id', '=', 'pp.id_jenis_pegawai')
            ->leftJoin('program_studi as ps', 'ps.id', '=', 'pb.id_progdi')
            ->leftJoinSub($latestJabatanSub, 'latest_jf', function ($join) {
                $join->on('latest_jf.id_pegawai', '=', 'pb.id_pegawai');
            })
            ->leftJoin('pegawai_jabatan_fungsional as pjf2', 'pjf2.id', '=', 'latest_jf.last_id')
            ->select([
                'pb.*',
                DB::raw("COALESCE(p.nama, '') as nama_pegawai"),
                DB::raw("COALESCE(pp.nama, '') as nama_posisi_pegawai"),
                DB::raw("COALESCE(pj.nama, '') as nama_jenis_pegawai"),
                DB::raw("COALESCE(ps.nama_jurusan, '') as nama_program_studi"),
                DB::raw("COALESCE(ps.jenjang, '') as nama_jenjang"),
                DB::raw("COALESCE(pjf2.jabatan_fungsional_sekarang, '') as nama_jabatan_fungsional"),
            ])
            ->orderBy('pb.nama_lengkap');

        if ($selectedPosisi !== '') {
            $query->where('pb.kd_posisi_pegawai', $selectedPosisi);
        }

        if ($selectedJabatanFungsional !== '') {
            $query->where('pjf2.jabatan_fungsional_sekarang', $selectedJabatanFungsional);
        }

        if ($selectedJenisKelamin !== '') {
            $query->where('pb.jenis_kelamin', $selectedJenisKelamin);
        }

        if ($selectedJenjang !== '') {
            $query->where('ps.jenjang', $selectedJenjang);
        }

        if ($selectedProgramStudi > 0) {
            $query->where('pb.id_progdi', $selectedProgramStudi);
        }

        if ($selectedStatus !== '') {
            $query->where('pb.status_pegawai', $selectedStatus);
        }

        return $query;
    }

    /**
     * Halaman KRS Per TA
     */
    public function krsPerTa()
    {
        $CurrentPage = 'content';
        $title = 'KRS Per TA';
        $backUrl = '/data';

        $tahunAjaranList = MasterTahunAjaran::query()
            ->orderByDesc('id')
            ->get();

        $selectedTahunAjaran = (int) request('id_tahun_ajaran', (int) ($tahunAjaranList->first()->id ?? 0));
        $isPreview = request()->boolean('preview');

        $tableColumns = [];
        $rows = collect();

        if ($isPreview && $selectedTahunAjaran > 0) {
            $tableColumns = ['kode_jadwal', 'kode_mata_kuliah', 'nama_mata_kuliah', 'hari', 'sesi', 'ruang', 'kelas', 'rombel', 'id_dosen', 'kuota_diambil', 'status', 'tipe_mhs_label', 'sumber'];
            $rows = $this->buildKrsPerTaQuery($selectedTahunAjaran);
        }

        return view('admin.data-support.krs-per-ta', compact(
            'CurrentPage',
            'title',
            'backUrl',
            'tahunAjaranList',
            'selectedTahunAjaran',
            'isPreview',
            'tableColumns',
            'rows'
        ));
    }

    /**
     * Export KRS Per TA ke Excel
     */
    public function exportKrsPerTaExcel(Request $request)
    {
        $selectedTahunAjaran = (int) $request->query('id_tahun_ajaran', 0);
        
        if ($selectedTahunAjaran <= 0) {
            return redirect()->back()->with('error', 'Tahun ajaran tidak valid.');
        }

        $tableColumns = ['kode_jadwal', 'kode_mata_kuliah', 'nama_mata_kuliah', 'hari', 'sesi', 'ruang', 'kelas', 'rombel', 'id_dosen', 'kuota_diambil', 'status', 'tipe_mhs_label', 'sumber'];
        $rows = $this->buildKrsPerTaQuery($selectedTahunAjaran);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('KRS Per TA');

        foreach ($tableColumns as $index => $header) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $rowNumber = 2;
        foreach ($rows as $row) {
            foreach ($tableColumns as $index => $columnName) {
                $column = Coordinate::stringFromColumnIndex($index + 1);
                $value = $row->{$columnName} ?? '';
                $sheet->setCellValue($column . $rowNumber, (string) $value);
            }
            $rowNumber++;
        }

        $fileName = 'krs-per-ta-' . now()->format('Ymd-His') . '.xlsx';
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    private function buildKrsPerTaQuery(int $selectedTahunAjaran)
    {
        $jadwalTemp = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->where('mjt.id_tahun', $selectedTahunAjaran)
            ->select([
                'mjt.kode_jadwal',
                'mjt.kode_mata_kuliah',
                DB::raw("COALESCE(mmk.nama_mata_kuliah, '') as nama_mata_kuliah"),
                'mjt.hari',
                'mjt.sesi',
                'mjt.ruang',
                'mjt.kelas',
                'mjt.rombel',
                'mjt.id_dosen',
                'mjt.kuota_diambil',
                'mjt.status',
                'mjt.tipe_mhs',
                DB::raw("CASE WHEN mjt.tipe_mhs = 1 THEN 'Reguler' WHEN mjt.tipe_mhs = 2 THEN 'RPL' ELSE 'Unknown' END as tipe_mhs_label"),
                DB::raw("'Temp' as sumber"),
            ])
            ->get();

        $jadwalHistory = DB::table('master_jadwal as mj')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mj.kode_mata_kuliah')
            ->where('mj.id_tahun', $selectedTahunAjaran)
            ->select([
                'mj.kode_jadwal',
                'mj.kode_mata_kuliah',
                DB::raw("COALESCE(mmk.nama_mata_kuliah, '') as nama_mata_kuliah"),
                'mj.hari',
                'mj.sesi',
                'mj.ruang',
                'mj.kelas',
                'mj.rombel',
                'mj.id_dosen',
                'mj.kuota_diambil',
                'mj.status',
                'mj.tipe_mhs',
                DB::raw("CASE WHEN mj.tipe_mhs = 1 THEN 'Reguler' WHEN mj.tipe_mhs = 2 THEN 'RPL' ELSE 'Unknown' END as tipe_mhs_label"),
                DB::raw("'History' as sumber"),
            ])
            ->get();

        return collect($jadwalTemp)->concat($jadwalHistory)->sortBy('kode_mata_kuliah');
    }
}
