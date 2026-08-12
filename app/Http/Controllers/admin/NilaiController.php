<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NilaiController extends Controller
{
    /**
     * Halaman 1: Daftar dosen yang mengampu jadwal
     */
    public function index()
    {
        $data['title'] = 'Input Nilai - Daftar Dosen';
        $data['CurrentPage'] = 'content';

        // Ambil dosen yang memiliki jadwal di master_jadwal_temp
        $data['dosen'] = DB::table('pegawai_biodata as pb')
            ->select(
                'pb.id',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen"),
                'pb.nidn',
                'pb.nip_pns',
                DB::raw("COUNT(DISTINCT mjt.id) as jumlah_jadwal")
            )
            ->join('master_jadwal_temp as mjt', 'mjt.id_dosen', '=', 'pb.id')
            ->where('pb.status_pegawai', 'aktif')
            ->groupBy('pb.id', 'pb.gelar_depan', 'pb.nama_lengkap', 'pb.gelar_belakang', 'pb.nidn', 'pb.nip_pns')
            ->get()
            ->sortBy(function ($item) {
                return strtolower(preg_replace('/\s+/', ' ', $item->nama_dosen));
            })->values();

        $data['no'] = 1;
        return view('admin.nilai.index', $data);
    }

    /**
     * Halaman 2: Daftar jadwal yang diampu oleh dosen
     */
    public function jadwal(string $id_dosen)
    {
        $dosen = DB::table('pegawai_biodata')->where('id', $id_dosen)->first();
        if (!$dosen) {
            return redirect('master/nilai')->with('error', 'Dosen tidak ditemukan.');
        }

        $data['title'] = 'Input Nilai - Jadwal Dosen';
        $data['CurrentPage'] = 'content';
        $data['dosen'] = $dosen;
        $data['id_dosen'] = $id_dosen;

        $data['jadwal'] = DB::table('master_jadwal_temp as mjt')
            ->select(
                'mjt.*',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                'mta.awal',
                'mta.akhir',
                'mta.jenis',
                DB::raw("COUNT(mk.id) as jumlah_mahasiswa")
            )
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->leftJoin('master_tahun_ajaran as mta', 'mta.id', '=', 'mjt.id_tahun')
            ->leftJoin('master_krs_temp as mk', function ($join) {
                $join->on('mk.id_jadwal', '=', 'mjt.id')
                    ->on('mk.id_tahun', '=', 'mjt.id_tahun');
            })
            ->where('mjt.id_dosen', $id_dosen)
            ->groupBy(
                'mjt.id',
                'mjt.kode_jadwal',
                'mjt.id_dosen',
                'mjt.id_dosen2',
                'mjt.id_tahun',
                'mjt.kode_mata_kuliah',
                'mjt.hari',
                'mjt.sesi',
                'mjt.ruang',
                'mjt.kelas',
                'mjt.rombel',
                'mjt.kuota_diambil',
                'mjt.status',
                'mjt.tipe_mhs',
                'mjt.rps',
                'mjt.kp',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                'mta.awal',
                'mta.akhir',
                'mta.jenis'
            )
            ->orderBy('mta.awal', 'desc')
            ->get();

        $data['no'] = 1;
        return view('admin.nilai.jadwal', $data);
    }

    /**
     * Halaman 3: Input nilai mahasiswa per jadwal
     */
    public function input(string $id_jadwal)
    {
        $jadwal = DB::table('master_jadwal_temp as mjt')
            ->select(
                'mjt.*',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                'mta.awal',
                'mta.akhir',
                'mta.jenis',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen")
            )
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->leftJoin('master_tahun_ajaran as mta', 'mta.id', '=', 'mjt.id_tahun')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mjt.id_dosen')
            ->where('mjt.id', $id_jadwal)
            ->first();

        if (!$jadwal) {
            return redirect('master/nilai')->with('error', 'Jadwal tidak ditemukan.');
        }

        // Ambil persentase nilai jika ada
        $persentase = DB::table('master_persentase_nilai')
            ->where('id_jadwal', $id_jadwal)
            ->first();

        // Ambil list mahasiswa dari master_krs_temp + gabung dengan master_nilai yang sudah ada
        $mahasiswa = DB::table('master_krs_temp as mk')
            ->select(
                'mk.nim',
                'mk.id_jadwal',
                'mk.id_tahun',
                'mhs.nama',
                'mhs.id_program_studi',
                'mn.id as nilai_id',
                'mn.ntugas',
                'mn.nuts',
                'mn.nuas',
                'mn.nakhir',
                'mn.nhuruf',
                'mn.ndosen'
            )
            ->leftJoin('mahasiswa as mhs', 'mhs.nim', '=', 'mk.nim')
            ->leftJoin('master_nilai as mn', function ($join) {
                $join->on('mn.nim', '=', 'mk.nim')
                    ->on('mn.id_jadwal', '=', 'mk.id_jadwal')
                    ->on('mn.id_tahun', '=', 'mk.id_tahun');
            })
            ->where('mk.id_jadwal', $id_jadwal)
            ->orderBy('mhs.nama')
            ->get();

        $data['title'] = 'Input Nilai Mahasiswa';
        $data['CurrentPage'] = 'content';
        $data['jadwal'] = $jadwal;
        $data['mahasiswa'] = $mahasiswa;
        $data['persentase'] = $persentase;
        $status = DB::table('master_nilai')
            ->where('id_jadwal', $id_jadwal)
            ->where('id_tahun', $jadwal->id_tahun)
            ->selectRaw('COALESCE(MAX(publish_tugas),0) as publish_tugas')
            ->selectRaw('COALESCE(MAX(publish_uts),0) as publish_uts')
            ->selectRaw('COALESCE(MAX(publish_uas),0) as publish_uas')
            ->selectRaw('COALESCE(MAX(validasi_tugas),0) as validasi_tugas')
            ->selectRaw('COALESCE(MAX(validasi_uts),0) as validasi_uts')
            ->selectRaw('COALESCE(MAX(validasi_uas),0) as validasi_uas')
            ->first();

        $data['publishStatus'] = [
            'tugas' => (int) ($status->publish_tugas ?? 0),
            'uts' => (int) ($status->publish_uts ?? 0),
            'uas' => (int) ($status->publish_uas ?? 0),
        ];

        $data['validasiStatus'] = [
            'tugas' => (int) ($status->validasi_tugas ?? 0),
            'uts' => (int) ($status->validasi_uts ?? 0),
            'uas' => (int) ($status->validasi_uas ?? 0),
        ];
        $data['no'] = 1;
        return view('admin.nilai.input', $data);
    }

    /**
     * Simpan nilai
     */
    public function save(Request $request)
    {
        $id_jadwal = $request->input('id_jadwal');
        $id_tahun = $request->input('id_tahun');
        $id_dosen = $request->input('id_dosen');
        $nims = $request->input('nim', []);
        $ntugas = $request->input('ntugas', []);
        $nuts = $request->input('nuts', []);
        $nuas = $request->input('nuas', []);

        // Ambil persentase nilai
        $persentase = DB::table('master_persentase_nilai')
            ->where('id_jadwal', $id_jadwal)
            ->first();

        $pct_tugas = $persentase ? (float) $persentase->ntugas : 30;
        $pct_uts = $persentase ? (float) $persentase->nuts : 35;
        $pct_uas = $persentase ? (float) $persentase->nuas : 35;

        foreach ($nims as $i => $nim) {
            $nt = isset($ntugas[$i]) && $ntugas[$i] !== '' ? (float) $ntugas[$i] : null;
            $nu = isset($nuts[$i]) && $nuts[$i] !== '' ? (float) $nuts[$i] : null;
            $nua = isset($nuas[$i]) && $nuas[$i] !== '' ? (float) $nuas[$i] : null;

            // Hitung nilai akhir jika semua nilai ada
            $nakhir = null;
            $nhuruf = null;
            if ($nt !== null && $nu !== null && $nua !== null) {
                $nakhir = round(($nt * $pct_tugas / 100) + ($nu * $pct_uts / 100) + ($nua * $pct_uas / 100), 2);
                $nhuruf = $this->hitungHuruf($nakhir);
            }

            DB::table('master_nilai')->updateOrInsert(
                [
                    'nim' => $nim,
                    'id_jadwal' => $id_jadwal,
                    'id_tahun' => $id_tahun,
                ],
                [
                    'ntugas' => $nt,
                    'nuts' => $nu,
                    'nuas' => $nua,
                    'nakhir' => $nakhir,
                    'nhuruf' => $nhuruf,
                    'ndosen' => $id_dosen,
                    'is_krs' => DB::raw('COALESCE(is_krs, 1)'),
                    'publish_tugas' => DB::raw('COALESCE(publish_tugas, 0)'),
                    'publish_uts' => DB::raw('COALESCE(publish_uts, 0)'),
                    'publish_uas' => DB::raw('COALESCE(publish_uas, 0)'),
                    'validasi_tugas' => DB::raw('COALESCE(validasi_tugas, 0)'),
                    'validasi_uts' => DB::raw('COALESCE(validasi_uts, 0)'),
                    'validasi_uas' => DB::raw('COALESCE(validasi_uas, 0)'),
                    'log_date' => now(),
                ]
            );
        }

        return redirect("master/nilai/input/{$id_jadwal}")
            ->with('status', 'Nilai berhasil disimpan!');
    }

    public function savePersentase(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|integer',
            'pct_tugas' => 'required|numeric|min:0|max:100',
            'pct_uts' => 'required|numeric|min:0|max:100',
            'pct_uas' => 'required|numeric|min:0|max:100',
        ]);

        $total = (float) $request->pct_tugas + (float) $request->pct_uts + (float) $request->pct_uas;
        if (round($total, 2) !== 100.0) {
            return redirect()->back()->with('error', 'Total persentase harus 100%.');
        }

        DB::table('master_persentase_nilai')->updateOrInsert(
            ['id_jadwal' => $request->id_jadwal],
            [
                'ntugas' => $request->pct_tugas,
                'nuts' => $request->pct_uts,
                'nuas' => $request->pct_uas,
                'datetime' => now(),
            ]
        );

        return redirect()->back()->with('status', 'Persentase nilai berhasil disimpan.');
    }

    public function togglePublish(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|integer',
            'id_tahun' => 'required|integer',
            'id_dosen' => 'required',
            'component' => 'required|in:tugas,uts,uas',
        ]);

        $this->ensureNilaiRows($request->id_jadwal, $request->id_tahun, $request->id_dosen);

        $fieldMap = [
            'tugas' => 'publish_tugas',
            'uts' => 'publish_uts',
            'uas' => 'publish_uas',
        ];
        $field = $fieldMap[$request->component];

        $curr = (int) DB::table('master_nilai')
            ->where('id_jadwal', $request->id_jadwal)
            ->where('id_tahun', $request->id_tahun)
            ->max($field);

        $next = $curr === 1 ? 0 : 1;

        DB::table('master_nilai')
            ->where('id_jadwal', $request->id_jadwal)
            ->where('id_tahun', $request->id_tahun)
            ->update([
                $field => $next,
                'log_date' => now(),
            ]);

        return redirect()->back()->with('status', 'Status publish berhasil diperbarui.');
    }

    public function toggleValidasi(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|integer',
            'id_tahun' => 'required|integer',
            'id_dosen' => 'required',
            'component' => 'required|in:tugas,uts,uas',
        ]);

        $this->ensureNilaiRows($request->id_jadwal, $request->id_tahun, $request->id_dosen);

        $fieldMap = [
            'tugas' => 'validasi_tugas',
            'uts' => 'validasi_uts',
            'uas' => 'validasi_uas',
        ];
        $field = $fieldMap[$request->component];

        $curr = (int) DB::table('master_nilai')
            ->where('id_jadwal', $request->id_jadwal)
            ->where('id_tahun', $request->id_tahun)
            ->max($field);

        $next = $curr === 1 ? 0 : 1;

        DB::table('master_nilai')
            ->where('id_jadwal', $request->id_jadwal)
            ->where('id_tahun', $request->id_tahun)
            ->update([
                $field => $next,
                'log_date' => now(),
            ]);

        return redirect()->back()->with('status', 'Status validasi berhasil diperbarui.');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|integer',
            'id_tahun' => 'required|integer',
            'id_dosen' => 'required',
            'nilai_file' => 'required|file|mimes:csv,txt,xlsx,xls',
        ]);

        $idJadwal = (int) $request->id_jadwal;
        $idTahun = (int) $request->id_tahun;
        $idDosen = $request->id_dosen;

        $this->ensureNilaiRows($idJadwal, $idTahun, $idDosen);

        $rows = $this->parseUploadedNilaiFile($request->file('nilai_file')->getRealPath(), $request->file('nilai_file')->getClientOriginalExtension());
        if (count($rows) === 0) {
            return redirect()->back()->with('error', 'File kosong atau format kolom tidak sesuai.');
        }

        $persentase = DB::table('master_persentase_nilai')->where('id_jadwal', $idJadwal)->first();
        $pctTugas = $persentase ? (float) $persentase->ntugas : 30;
        $pctUts = $persentase ? (float) $persentase->nuts : 35;
        $pctUas = $persentase ? (float) $persentase->nuas : 35;

        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $nim = trim((string) ($row['nim'] ?? ''));
            if ($nim === '') {
                $skipped++;
                continue;
            }

            $exists = DB::table('master_krs_temp')
                ->where('id_jadwal', $idJadwal)
                ->where('id_tahun', $idTahun)
                ->where('nim', $nim)
                ->exists();

            if (!$exists) {
                $skipped++;
                continue;
            }

            $nt = $row['ntugas'];
            $nu = $row['nuts'];
            $na = $row['nuas'];

            $nakhir = null;
            $nhuruf = null;
            if ($nt !== null && $nu !== null && $na !== null) {
                $nakhir = round(($nt * $pctTugas / 100) + ($nu * $pctUts / 100) + ($na * $pctUas / 100), 2);
                $nhuruf = $this->hitungHuruf($nakhir);
            }

            DB::table('master_nilai')->updateOrInsert(
                [
                    'nim' => $nim,
                    'id_jadwal' => $idJadwal,
                    'id_tahun' => $idTahun,
                ],
                [
                    'ntugas' => $nt,
                    'nuts' => $nu,
                    'nuas' => $na,
                    'nakhir' => $nakhir,
                    'nhuruf' => $nhuruf,
                    'ndosen' => $idDosen,
                    'is_krs' => DB::raw('COALESCE(is_krs, 1)'),
                    'publish_tugas' => DB::raw('COALESCE(publish_tugas, 0)'),
                    'publish_uts' => DB::raw('COALESCE(publish_uts, 0)'),
                    'publish_uas' => DB::raw('COALESCE(publish_uas, 0)'),
                    'validasi_tugas' => DB::raw('COALESCE(validasi_tugas, 0)'),
                    'validasi_uts' => DB::raw('COALESCE(validasi_uts, 0)'),
                    'validasi_uas' => DB::raw('COALESCE(validasi_uas, 0)'),
                    'log_date' => now(),
                ]
            );

            $imported++;
        }

        return redirect()->back()->with('status', "Upload selesai: {$imported} baris diproses, {$skipped} baris dilewati.");
    }

    public function downloadTemplate()
    {
        $content = "nim,ntugas,nuts,nuas\n";
        $content .= "A1242013,80,78,85\n";
        $content .= "A1242014,75,70,80\n";

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_input_nilai.csv"',
        ]);
    }

    private function ensureNilaiRows(int $idJadwal, int $idTahun, $idDosen): void
    {
        $krsRows = DB::table('master_krs_temp')
            ->where('id_jadwal', $idJadwal)
            ->where('id_tahun', $idTahun)
            ->select('nim')
            ->get();

        foreach ($krsRows as $krs) {
            DB::table('master_nilai')->updateOrInsert(
                [
                    'nim' => $krs->nim,
                    'id_jadwal' => $idJadwal,
                    'id_tahun' => $idTahun,
                ],
                [
                    'ndosen' => $idDosen,
                    'is_krs' => DB::raw('COALESCE(is_krs, 1)'),
                    'publish_tugas' => DB::raw('COALESCE(publish_tugas, 0)'),
                    'publish_uts' => DB::raw('COALESCE(publish_uts, 0)'),
                    'publish_uas' => DB::raw('COALESCE(publish_uas, 0)'),
                    'validasi_tugas' => DB::raw('COALESCE(validasi_tugas, 0)'),
                    'validasi_uts' => DB::raw('COALESCE(validasi_uts, 0)'),
                    'validasi_uas' => DB::raw('COALESCE(validasi_uas, 0)'),
                    'log_date' => now(),
                ]
            );
        }
    }

    private function parseUploadedNilaiFile(string $path, string $ext): array
    {
        $ext = strtolower($ext);
        $rows = [];

        if ($ext === 'csv' || $ext === 'txt') {
            $handle = fopen($path, 'r');
            if (!$handle) {
                return [];
            }

            $header = fgetcsv($handle);
            if (!$header) {
                fclose($handle);
                return [];
            }

            $map = $this->mapHeader($header);
            while (($data = fgetcsv($handle)) !== false) {
                $rows[] = $this->extractRow($data, $map);
            }
            fclose($handle);
            return $rows;
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $arr = $sheet->toArray(null, true, true, false);

        if (count($arr) < 2) {
            return [];
        }

        $header = $arr[0];
        $map = $this->mapHeader($header);

        for ($i = 1; $i < count($arr); $i++) {
            $rows[] = $this->extractRow($arr[$i], $map);
        }

        return $rows;
    }

    private function mapHeader(array $header): array
    {
        $map = ['nim' => null, 'ntugas' => null, 'nuts' => null, 'nuas' => null];

        foreach ($header as $idx => $label) {
            $col = strtolower(trim((string) $label));
            if ($col === 'nim')
                $map['nim'] = $idx;
            if ($col === 'ntugas' || $col === 'tugas')
                $map['ntugas'] = $idx;
            if ($col === 'nuts' || $col === 'uts')
                $map['nuts'] = $idx;
            if ($col === 'nuas' || $col === 'uas')
                $map['nuas'] = $idx;
        }

        return $map;
    }

    private function extractRow(array $row, array $map): array
    {
        $get = function ($key) use ($row, $map) {
            $idx = $map[$key];
            return $idx === null ? null : ($row[$idx] ?? null);
        };

        return [
            'nim' => trim((string) $get('nim')),
            'ntugas' => $this->normalizeScore($get('ntugas')),
            'nuts' => $this->normalizeScore($get('nuts')),
            'nuas' => $this->normalizeScore($get('nuas')),
        ];
    }

    private function normalizeScore($value): ?float
    {
        if ($value === null)
            return null;
        $v = trim((string) $value);
        if ($v === '')
            return null;
        $v = str_replace(',', '.', $v);
        if (!is_numeric($v))
            return null;
        $n = (float) $v;
        if ($n < 0)
            $n = 0;
        if ($n > 100)
            $n = 100;
        return $n;
    }

    /**
     * Hitung nilai huruf berdasarkan nilai akhir
     */
    private function hitungHuruf(float $nilai): string
    {
        if ($nilai >= 85)
            return 'A';
        if ($nilai >= 80)
            return 'AB';
        if ($nilai >= 75)
            return 'B';
        if ($nilai >= 70)
            return 'BC';
        if ($nilai >= 60)
            return 'C';
        if ($nilai >= 50)
            return 'D';
        return 'E';
    }
}
