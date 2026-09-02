<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MasterTahunAjaran;
use App\Models\TblNilaiKuesioner;
use App\Models\TblSoalKuesioner;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KuesionerController extends Controller
{
    public function index()
    {
        $tahunAjaranList = MasterTahunAjaran::query()
            ->select([
                'id',
                'awal',
                'akhir',
                'jenis',
                'tipe_mhs',
                'is_aktif',
            ])
            ->selectSub(function ($q) {
                $q->from('tbl_soal_kuesioner as s')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('s.id_ta', 'master_tahun_ajaran.id');
            }, 'total_soal')
            ->selectSub(function ($q) {
                $q->from('tbl_nilai_kuesioner as n')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('n.id_ta', 'master_tahun_ajaran.id');
            }, 'total_jawaban')
            ->orderByDesc('id_tahun')
            ->orderByDesc('id')
            ->get();

        return view('admin.kuesioner.index', [
            'title' => 'Kuesioner',
            'CurrentPage' => 'content',
            'tahunAjaranList' => $tahunAjaranList,
        ]);
    }

    public function soal(string $id_tahun)
    {
        $idTahun = (int) $id_tahun;
        $tahun = MasterTahunAjaran::find($idTahun);
        $categoryOptions = TblSoalKuesioner::categoryOptions();

        if (!$tahun) {
            return redirect()->to(url('akademik/kuesioner'))->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $soalList = TblSoalKuesioner::query()
            ->where('id_ta', $idTahun)
            ->orderByRaw('CAST(no_soal as UNSIGNED), no_soal')
            ->orderBy('id')
            ->get();

        return view('admin.kuesioner.soal', [
            'title' => 'Daftar Soal Kuesioner',
            'CurrentPage' => 'content',
            'tahun' => $tahun,
            'soalList' => $soalList,
            'categoryOptions' => $categoryOptions,
        ]);
    }

    public function storeSoal(Request $request, string $id_tahun)
    {
        $idTahun = (int) $id_tahun;
        $tahun = MasterTahunAjaran::find($idTahun);

        if (!$tahun) {
            return redirect()->to(url('akademik/kuesioner'))->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $categoryOptions = TblSoalKuesioner::categoryOptions();

        $validated = $request->validate([
            'no_soal' => 'required|string|max:20',
            'soal' => 'required|string',
            'category' => 'required|integer|in:' . implode(',', array_keys($categoryOptions)),
        ]);

        TblSoalKuesioner::create([
            'id_ta' => $idTahun,
            'no_soal' => trim($validated['no_soal']),
            'soal' => trim($validated['soal']),
            'category' => (int) $validated['category'],
        ]);

        return redirect()->to(url('akademik/kuesioner/soal/' . $idTahun))->with('status', 'Soal kuesioner berhasil ditambahkan.');
    }

    public function duplicateSoalDariSemesterSebelumnya(string $id_tahun)
    {
        $idTahun = (int) $id_tahun;
        $tahun = MasterTahunAjaran::find($idTahun);

        if (!$tahun) {
            return redirect()->to(url('akademik/kuesioner'))->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $totalSoalSaatIni = TblSoalKuesioner::query()
            ->where('id_ta', $idTahun)
            ->count();

        if ($totalSoalSaatIni > 0) {
            return redirect()->to(url('akademik/kuesioner/soal/' . $idTahun))
                ->with('error', 'Duplicate hanya tersedia saat daftar soal pada semester ini masih kosong.');
        }

        $tahunSebelumnya = MasterTahunAjaran::query()
            ->where('tipe_mhs', (int) $tahun->tipe_mhs)
            ->where(function ($q) use ($tahun) {
                $q->where('id_tahun', '<', (int) $tahun->id_tahun)
                    ->orWhere(function ($q2) use ($tahun) {
                        $q2->where('id_tahun', (int) $tahun->id_tahun)
                            ->where('id', '<', (int) $tahun->id);
                    });
            })
            ->whereExists(function ($q) {
                $q->from('tbl_soal_kuesioner as s')
                    ->selectRaw('1')
                    ->whereColumn('s.id_ta', 'master_tahun_ajaran.id');
            })
            ->orderByDesc('id_tahun')
            ->orderByDesc('id')
            ->first();

        if (!$tahunSebelumnya) {
            return redirect()->to(url('akademik/kuesioner/soal/' . $idTahun))
                ->with('error', 'Data soal semester sebelumnya tidak ditemukan untuk tipe mahasiswa ini.');
        }

        $soalSumber = TblSoalKuesioner::query()
            ->where('id_ta', (int) $tahunSebelumnya->id)
            ->orderByRaw('CAST(no_soal as UNSIGNED), no_soal')
            ->orderBy('id')
            ->get(['no_soal', 'soal', 'category']);

        if ($soalSumber->isEmpty()) {
            return redirect()->to(url('akademik/kuesioner/soal/' . $idTahun))
                ->with('error', 'Data soal semester sebelumnya kosong.');
        }

        DB::transaction(function () use ($idTahun, $soalSumber) {
            foreach ($soalSumber as $item) {
                TblSoalKuesioner::create([
                    'id_ta' => $idTahun,
                    'no_soal' => (string) $item->no_soal,
                    'soal' => (string) $item->soal,
                    'category' => (int) $item->category,
                ]);
            }
        });

        return redirect()->to(url('akademik/kuesioner/soal/' . $idTahun))
            ->with('status', 'Berhasil duplicate ' . $soalSumber->count() . ' soal dari semester sebelumnya.');
    }

    public function updateSoal(Request $request, string $id_tahun, string $id)
    {
        $idTahun = (int) $id_tahun;
        $idSoal = (int) $id;

        $soal = TblSoalKuesioner::query()
            ->where('id', $idSoal)
            ->where('id_ta', $idTahun)
            ->first();

        if (!$soal) {
            return redirect()->to(url('akademik/kuesioner/soal/' . $idTahun))->with('error', 'Data soal tidak ditemukan.');
        }

        $categoryOptions = TblSoalKuesioner::categoryOptions();

        $validated = $request->validate([
            'no_soal' => 'required|string|max:20',
            'soal' => 'required|string',
            'category' => 'required|integer|in:' . implode(',', array_keys($categoryOptions)),
        ]);

        $soal->update([
            'no_soal' => trim($validated['no_soal']),
            'soal' => trim($validated['soal']),
            'category' => (int) $validated['category'],
        ]);

        return redirect()->to(url('akademik/kuesioner/soal/' . $idTahun))->with('status', 'Soal kuesioner berhasil diperbarui.');
    }

    public function destroySoal(string $id_tahun, string $id)
    {
        $idTahun = (int) $id_tahun;
        $idSoal = (int) $id;

        $soal = TblSoalKuesioner::query()
            ->where('id', $idSoal)
            ->where('id_ta', $idTahun)
            ->first();

        if (!$soal) {
            return redirect()->to(url('akademik/kuesioner/soal/' . $idTahun))->with('error', 'Data soal tidak ditemukan.');
        }

        $soal->delete();

        return redirect()->to(url('akademik/kuesioner/soal/' . $idTahun))->with('status', 'Soal kuesioner berhasil dihapus.');
    }

    public function jawaban(Request $request, string $id_tahun)
    {
        $idTahun = (int) $id_tahun;
        $tahun = MasterTahunAjaran::find($idTahun);

        if (!$tahun) {
            return redirect()->to(url('akademik/kuesioner'))->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $selectedJadwalId = (int) $request->query('id_jadwal', 0);
        $selectedDosenId = (int) $request->query('id_dosen', 0);
        $shouldShowData = (int) $request->query('tampilkan', 0) === 1;
        $filterError = null;
        $questionHeaders = collect();
        $jawabanMatrix = collect();
        $grandAverage = null;

        $jadwalFromTemp = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk_temp', 'mmk_temp.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->select([
                DB::raw('mjt.id as id_jadwal'),
                'mjt.id_dosen',
                'mjt.id_dosen2',
                DB::raw('NULLIF(mjt.kode_jadwal, "") as kode_jadwal'),
                DB::raw('COALESCE(NULLIF(mmk_temp.nama_mata_kuliah, ""), NULLIF(mjt.kode_mata_kuliah, "")) as nama_mata_kuliah'),
                DB::raw('NULLIF(mjt.kelas, "") as kelas'),
                DB::raw('NULLIF(mjt.rombel, "") as rombel'),
                DB::raw("CONCAT(COALESCE(NULLIF(mjt.kode_jadwal, ''), CONCAT('Jadwal #', mjt.id)), ' - ', COALESCE(NULLIF(mmk_temp.nama_mata_kuliah, ''), NULLIF(mjt.kode_mata_kuliah, ''), 'Mata Kuliah')) as label_jadwal"),
            ])
            ->where('mjt.id_tahun', $idTahun)
            ->where('mjt.tipe_mhs', (int) ($tahun->tipe_mhs ?? 1))
            ->get();

        $jadwalFromHistory = DB::table('master_jadwal as mj')
            ->leftJoin('master_mata_kuliah as mmk_hist', 'mmk_hist.kode_mata_kuliah', '=', 'mj.kode_mata_kuliah')
            ->select([
                'mj.id_jadwal',
                'mj.id_dosen',
                'mj.id_dosen2',
                DB::raw('NULLIF(mj.kode_jadwal, "") as kode_jadwal'),
                DB::raw('COALESCE(NULLIF(mmk_hist.nama_mata_kuliah, ""), NULLIF(mj.kode_mata_kuliah, "")) as nama_mata_kuliah'),
                DB::raw('NULLIF(mj.kelas, "") as kelas'),
                DB::raw('NULLIF(mj.rombel, "") as rombel'),
                DB::raw("CONCAT(COALESCE(NULLIF(mj.kode_jadwal, ''), CONCAT('Jadwal #', mj.id_jadwal)), ' - ', COALESCE(NULLIF(mmk_hist.nama_mata_kuliah, ''), NULLIF(mj.kode_mata_kuliah, ''), 'Mata Kuliah')) as label_jadwal"),
            ])
            ->where('mj.id_tahun', $idTahun)
            ->where('mj.tipe_mhs', (int) ($tahun->tipe_mhs ?? 1))
            ->get();

        $jadwalOptions = $jadwalFromTemp
            ->concat($jadwalFromHistory)
            ->unique(function ($row) {
                return (int) ($row->id_jadwal ?? 0);
            })
            ->sortBy(function ($row) {
                return (int) ($row->id_jadwal ?? 0);
            })
            ->values();

        if ($selectedJadwalId > 0 && !$jadwalOptions->contains(fn($x) => (int) $x->id_jadwal === $selectedJadwalId)) {
            $selectedJadwalId = 0;
            $selectedDosenId = 0;
        }

        $dosenOptions = collect();
        if ($selectedJadwalId > 0) {
            $jadwalSelected = $jadwalOptions->first(fn($x) => (int) $x->id_jadwal === $selectedJadwalId);

            $dosenIds = collect([
                (int) ($jadwalSelected->id_dosen ?? 0),
                (int) ($jadwalSelected->id_dosen2 ?? 0),
            ])->filter(fn($id) => $id > 0)->unique()->values();

            if ($dosenIds->isNotEmpty()) {
                $dosenOptions = DB::table('pegawai_biodata as pb')
                    ->select([
                        'pb.id as id_dosen',
                        DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen"),
                    ])
                    ->whereIn('pb.id', $dosenIds->all())
                    ->orderBy('nama_dosen')
                    ->get();
            }

            if ($selectedDosenId > 0 && !$dosenOptions->contains(fn($x) => (int) $x->id_dosen === $selectedDosenId)) {
                $selectedDosenId = 0;
            }
        }

        $jawabanList = collect();
        if ($shouldShowData) {
            if ($selectedJadwalId <= 0) {
                $filterError = 'Silakan pilih jadwal terlebih dahulu.';
            } else {
                $jawabanQuery = TblNilaiKuesioner::query()
                    ->from('tbl_nilai_kuesioner as nk')
                    ->leftJoin('tbl_soal_kuesioner as sk', 'sk.id', '=', 'nk.id_kuesioner')
                    ->leftJoin('mahasiswa as m', 'm.nim', '=', 'nk.nim')
                    ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'nk.id_dosen')
                    ->select([
                        'nk.id',
                        'nk.nim',
                        'm.nama as nama_mahasiswa',
                        'nk.id_jadwal',
                        'nk.id_dosen',
                        DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen"),
                        'nk.nilai',
                        'sk.no_soal',
                        'sk.soal',
                        'sk.category',
                    ])
                    ->where('nk.id_ta', $idTahun)
                    ->where('nk.id_jadwal', $selectedJadwalId);

                if ($selectedDosenId > 0) {
                    $jawabanQuery->where('nk.id_dosen', $selectedDosenId);
                }

                $jawabanList = $jawabanQuery
                    ->orderBy('nk.id')
                    ->get();

                if ($jawabanList->isNotEmpty()) {
                    $questionHeaders = $jawabanList
                        ->pluck('no_soal')
                        ->filter(fn($value) => trim((string) $value) !== '')
                        ->unique()
                        ->sortBy(function ($value) {
                            return (int) $value;
                        })
                        ->values();

                    $jawabanMatrix = $jawabanList
                        ->groupBy(fn($row) => (string) ($row->nim ?? ''))
                        ->map(function ($rows) use ($questionHeaders) {
                            $first = $rows->first();

                            $nilaiBySoal = $rows->mapWithKeys(function ($row) {
                                return [(string) $row->no_soal => (float) $row->nilai];
                            });

                            $scores = [];
                            foreach ($questionHeaders as $noSoal) {
                                $key = (string) $noSoal;
                                $scores[$key] = $nilaiBySoal->has($key) ? (float) $nilaiBySoal->get($key) : null;
                            }

                            $avg = collect($scores)
                                ->filter(fn($nilai) => $nilai !== null)
                                ->avg();

                            return (object) [
                                'nim' => $first->nim,
                                'nama_mahasiswa' => $first->nama_mahasiswa,
                                'scores' => $scores,
                                'avg' => $avg,
                            ];
                        })
                        ->sortBy(function ($row) {
                            return trim((string) ($row->nama_mahasiswa ?? ''));
                        })
                        ->values();

                    $grandAverage = round((float) $jawabanMatrix
                        ->pluck('avg')
                        ->filter(fn($avg) => $avg !== null)
                        ->avg(), 2);
                }
            }
        }

        return view('admin.kuesioner.jawaban', [
            'title' => 'Jawaban Kuesioner',
            'CurrentPage' => 'content',
            'tahun' => $tahun,
            'jawabanList' => $jawabanList,
            'jadwalOptions' => $jadwalOptions,
            'dosenOptions' => $dosenOptions,
            'selectedJadwalId' => $selectedJadwalId,
            'selectedDosenId' => $selectedDosenId,
            'shouldShowData' => $shouldShowData,
            'filterError' => $filterError,
            'questionHeaders' => $questionHeaders,
            'jawabanMatrix' => $jawabanMatrix,
            'grandAverage' => $grandAverage,
            'categoryOptions' => TblSoalKuesioner::categoryOptions(),
        ]);
    }

    public function rekap(Request $request, string $id_tahun)
    {
        $idTahun = (int) $id_tahun;
        $tahun = MasterTahunAjaran::find($idTahun);

        if (!$tahun) {
            return redirect()->to(url('akademik/kuesioner'))->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $selectedJadwalId = (int) $request->query('id_jadwal', 0);
        $selectedDosenId = (int) $request->query('id_dosen', 0);
        $shouldShowData = (int) $request->query('tampilkan', 0) === 1;
        $filterError = null;

        $jadwalOptions = $this->getJadwalOptions($tahun, $idTahun);

        if ($selectedJadwalId > 0 && !$jadwalOptions->contains(fn($x) => (int) $x->id_jadwal === $selectedJadwalId)) {
            $selectedJadwalId = 0;
            $selectedDosenId = 0;
        }

        $dosenOptions = $this->getDosenOptions($jadwalOptions, $selectedJadwalId);

        if ($selectedDosenId > 0 && !$dosenOptions->contains(fn($x) => (int) $x->id_dosen === $selectedDosenId)) {
            $selectedDosenId = 0;
        }

        $rekapData = [
            'rekapList' => collect(),
            'rekapGroups' => collect(),
            'summary' => [
                'count_sts' => 0,
                'count_ts' => 0,
                'count_s' => 0,
                'count_ss' => 0,
                'total_jawaban' => 0,
                'rata_nilai' => 0,
            ],
        ];

        if ($shouldShowData) {
            if ($selectedJadwalId <= 0) {
                $filterError = 'Silakan pilih jadwal terlebih dahulu.';
            } else {
                $rekapData = $this->buildRekapSummary($idTahun, $selectedJadwalId, $selectedDosenId);
            }
        }

        return view('admin.kuesioner.rekap', [
            'title' => 'Rekap Kuesioner',
            'CurrentPage' => 'content',
            'tahun' => $tahun,
            'rekapList' => $rekapData['rekapList'],
            'rekapGroups' => $rekapData['rekapGroups'],
            'summary' => $rekapData['summary'],
            'jadwalOptions' => $jadwalOptions,
            'dosenOptions' => $dosenOptions,
            'selectedJadwalId' => $selectedJadwalId,
            'selectedDosenId' => $selectedDosenId,
            'shouldShowData' => $shouldShowData,
            'filterError' => $filterError,
            'categoryOptions' => TblSoalKuesioner::categoryOptions(),
        ]);
    }

    public function exportRekapExcel(Request $request, string $id_tahun)
    {
        $idTahun = (int) $id_tahun;
        $tahun = MasterTahunAjaran::find($idTahun);

        if (!$tahun) {
            return redirect()->to(url('akademik/kuesioner'))->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $selectedJadwalId = (int) $request->query('id_jadwal', 0);
        $selectedDosenId = (int) $request->query('id_dosen', 0);

        if ($selectedJadwalId <= 0) {
            return redirect()->to(url('akademik/kuesioner/rekap/' . $idTahun))
                ->with('error', 'Silakan pilih jadwal terlebih dahulu sebelum export.');
        }

        $rekapData = $this->buildRekapSummary($idTahun, $selectedJadwalId, $selectedDosenId);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $jenisLabel = (int) $tahun->jenis === 1 ? 'Ganjil' : ((int) $tahun->jenis === 2 ? 'Genap' : '-');
        $tipeLabel = (int) $tahun->tipe_mhs === 2 ? 'RPL' : 'Reguler';

        $sheet->setCellValue('A1', 'Rekap Hasil Kuesioner');
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A2', 'TA ' . $tahun->awal . '/' . $tahun->akhir . ' (' . $jenisLabel . ') - ' . $tipeLabel);
        $sheet->mergeCells('A2:G2');

        $sheet->fromArray([
            ['Soal', 'Sangat Tidak Setuju', 'Tidak Setuju', 'Setuju', 'Sangat Setuju', 'Total Jawaban', 'Rata-rata'],
        ], null, 'A4');

        $rowIndex = 5;
        foreach ($rekapData['rekapGroups'] as $group) {
            $sheet->setCellValue('A' . $rowIndex, $group['label']);
            $sheet->mergeCells('A' . $rowIndex . ':G' . $rowIndex);
            $rowIndex++;

            foreach ($group['items'] as $item) {
                $sheet->fromArray([
                    [
                        'Q' . $item->no_soal . ' - ' . $item->soal,
                        (int) $item->count_sts,
                        (int) $item->count_ts,
                        (int) $item->count_s,
                        (int) $item->count_ss,
                        (int) $item->total_jawaban,
                        (float) $item->rata_nilai,
                    ],
                ], null, 'A' . $rowIndex);
                $rowIndex++;
            }
        }

        $sheet->fromArray([
            ['Jumlah', (int) $rekapData['summary']['count_sts'], (int) $rekapData['summary']['count_ts'], (int) $rekapData['summary']['count_s'], (int) $rekapData['summary']['count_ss'], (int) $rekapData['summary']['total_jawaban'], ''],
            ['Rata-rata', '', '', '', '', '', (float) $rekapData['summary']['rata_nilai']],
        ], null, 'A' . $rowIndex);

        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = 'rekap-kuesioner-' . $idTahun . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'rekap_kuesioner_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    public function exportRekapPdf(Request $request, string $id_tahun)
    {
        $idTahun = (int) $id_tahun;
        $tahun = MasterTahunAjaran::find($idTahun);

        if (!$tahun) {
            return redirect()->to(url('akademik/kuesioner'))->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $selectedJadwalId = (int) $request->query('id_jadwal', 0);
        $selectedDosenId = (int) $request->query('id_dosen', 0);

        if ($selectedJadwalId <= 0) {
            return redirect()->to(url('akademik/kuesioner/rekap/' . $idTahun))
                ->with('error', 'Silakan pilih jadwal terlebih dahulu sebelum export.');
        }

        $jadwalOptions = $this->getJadwalOptions($tahun, $idTahun);

        $jadwal = null;
        if ($selectedJadwalId > 0) {
            $jadwal = $jadwalOptions->first(fn($x) => (int) $x->id_jadwal === $selectedJadwalId);
        }

        $dosen = null;
        if ($selectedDosenId > 0) {
            $dosenOptions = $this->getDosenOptions($jadwalOptions, $selectedJadwalId);
            $dosen = $dosenOptions->first(fn($x) => (int) $x->id_dosen === $selectedDosenId);
        }

        $rekapData = $this->buildRekapSummary($idTahun, $selectedJadwalId, $selectedDosenId);
        $html = view('admin.kuesioner.rekap_pdf', [
            'tahun' => $tahun,
            'rekapGroups' => $rekapData['rekapGroups'],
            'summary' => $rekapData['summary'],
            'jadwal' => $jadwal,
            'dosen' => $dosen,
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'dejavusans',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('rekap-kuesioner-' . $idTahun . '.pdf', 'D'))
            ->header('Content-Type', 'application/pdf');
    }

    public function exportRekapExcelAll(Request $request, string $id_tahun)
    {
        set_time_limit(300); // Allow up to 5 minutes for heavy export
        ini_set('memory_limit', '512M');
        $idTahun = (int) $id_tahun;
        $tahun = MasterTahunAjaran::find($idTahun);

        if (!$tahun) {
            return redirect()->to(url('akademik/kuesioner'))->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $jadwalOptions = $this->getJadwalOptions($tahun, $idTahun);

        $pairs = \Illuminate\Support\Facades\DB::table('tbl_nilai_kuesioner')
            ->where('id_ta', $idTahun)
            ->select('id_jadwal', 'id_dosen')
            ->distinct()
            ->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $jenisLabel = (int) $tahun->jenis === 1 ? 'Ganjil' : ((int) $tahun->jenis === 2 ? 'Genap' : '-');
        $tipeLabel = (int) $tahun->tipe_mhs === 2 ? 'RPL' : 'Reguler';

        foreach ($pairs as $idx => $pair) {
            $selectedJadwalId = (int) $pair->id_jadwal;
            $selectedDosenId = (int) $pair->id_dosen;

            $jadwal = $jadwalOptions->first(fn($x) => (int) $x->id_jadwal === $selectedJadwalId);
            $dosenOptions = $this->getDosenOptions($jadwalOptions, $selectedJadwalId);
            $dosen = $dosenOptions->first(fn($x) => (int) $x->id_dosen === $selectedDosenId);

            $rekapData = $this->buildRekapSummary($idTahun, $selectedJadwalId, $selectedDosenId);

            $sheetName = substr(($jadwal->kode_mata_kuliah ?? 'Mk') . '-' . ($dosen->nama_dosen ?? 'Dosen'), 0, 30);
            // safe sheet name
            $sheetName = preg_replace('/[*\:\/\\\?\[\]]/', '', $sheetName);

            $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $sheetName);
            $spreadsheet->addSheet($sheet, $idx);

            $sheet->setCellValue('A1', 'Rekap Hasil Kuesioner');
            $sheet->mergeCells('A1:G1');
            $sheet->setCellValue('A2', 'TA ' . $tahun->awal . '/' . $tahun->akhir . ' (' . $jenisLabel . ') - ' . $tipeLabel);
            $sheet->mergeCells('A2:G2');

            if ($jadwal) {
                $sheet->setCellValue('A3', 'Mata Kuliah: ' . $jadwal->kode_mata_kuliah . ' - ' . ($jadwal->nama_mata_kuliah ?? '-'));
                $sheet->mergeCells('A3:G3');
            }
            if ($dosen) {
                $sheet->setCellValue('A4', 'Dosen: ' . ($dosen->nama_dosen ?? '-'));
                $sheet->mergeCells('A4:G4');
            }

            $sheet->fromArray([
                ['Soal', 'Sangat Tidak Setuju', 'Tidak Setuju', 'Setuju', 'Sangat Setuju', 'Total Jawaban', 'Rata-rata'],
            ], null, 'A6');

            $rowIndex = 7;
            foreach ($rekapData['rekapGroups'] as $group) {
                $sheet->setCellValue('A' . $rowIndex, $group['label']);
                $sheet->mergeCells('A' . $rowIndex . ':G' . $rowIndex);
                $rowIndex++;

                foreach ($group['items'] as $item) {
                    $sheet->fromArray([
                        [
                            'Q' . $item->no_soal . ' - ' . $item->soal,
                            (int) $item->count_sts,
                            (int) $item->count_ts,
                            (int) $item->count_s,
                            (int) $item->count_ss,
                            (int) $item->total_jawaban,
                            (float) $item->rata_nilai,
                        ],
                    ], null, 'A' . $rowIndex);
                    $rowIndex++;
                }
            }

            $sheet->fromArray([
                ['Jumlah', (int) $rekapData['summary']['count_sts'], (int) $rekapData['summary']['count_ts'], (int) $rekapData['summary']['count_s'], (int) $rekapData['summary']['count_ss'], (int) $rekapData['summary']['total_jawaban'], ''],
                ['Rata-rata', '', '', '', '', '', (float) $rekapData['summary']['rata_nilai']],
            ], null, 'A' . $rowIndex);

            foreach (range('A', 'G') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
        }

        if ($spreadsheet->getSheetCount() == 0) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setCellValue('A1', 'Tidak ada data kuesioner');
            $spreadsheet->setActiveSheetIndex(0);
        }

        $filename = 'rekap-kuesioner-all-' . $idTahun . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'rekap_kuesioner_all_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    public function exportRekapPdfAll(Request $request, string $id_tahun)
    {
        set_time_limit(300); // Allow up to 5 minutes for heavy export
        ini_set('memory_limit', '512M');
        $idTahun = (int) $id_tahun;
        $tahun = MasterTahunAjaran::find($idTahun);

        if (!$tahun) {
            return redirect()->to(url('akademik/kuesioner'))->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $jadwalOptions = $this->getJadwalOptions($tahun, $idTahun);

        $pairs = \Illuminate\Support\Facades\DB::table('tbl_nilai_kuesioner')
            ->where('id_ta', $idTahun)
            ->select('id_jadwal', 'id_dosen')
            ->distinct()
            ->get();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'default_font' => 'dejavusans',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        if ($pairs->isEmpty()) {
            $mpdf->WriteHTML('<h3>Tidak ada data kuesioner.</h3>');
        } else {
            foreach ($pairs as $idx => $pair) {
                $selectedJadwalId = (int) $pair->id_jadwal;
                $selectedDosenId = (int) $pair->id_dosen;

                $jadwal = $jadwalOptions->first(fn($x) => (int) $x->id_jadwal === $selectedJadwalId);
                $dosenOptions = $this->getDosenOptions($jadwalOptions, $selectedJadwalId);
                $dosen = $dosenOptions->first(fn($x) => (int) $x->id_dosen === $selectedDosenId);

                $rekapData = $this->buildRekapSummary($idTahun, $selectedJadwalId, $selectedDosenId);
                $html = view('admin.kuesioner.rekap_pdf', [
                    'tahun' => $tahun,
                    'rekapGroups' => $rekapData['rekapGroups'],
                    'summary' => $rekapData['summary'],
                    'jadwal' => $jadwal,
                    'dosen' => $dosen,
                ])->render();

                if ($idx > 0) {
                    $mpdf->AddPage();
                }
                $mpdf->WriteHTML($html);
            }
        }

        return response($mpdf->Output('rekap-kuesioner-all-' . $idTahun . '.pdf', 'D'))
            ->header('Content-Type', 'application/pdf');
    }

    private function buildRekapSummary(int $idTahun, int $selectedJadwalId = 0, int $selectedDosenId = 0): array
    {
        $rekapList = TblSoalKuesioner::query()
            ->from('tbl_soal_kuesioner as sk')
            ->leftJoin('tbl_nilai_kuesioner as nk', function ($join) use ($idTahun, $selectedJadwalId, $selectedDosenId) {
                $join->on('nk.id_kuesioner', '=', 'sk.id');

                $join->where('nk.id_ta', '=', $idTahun);

                if ($selectedJadwalId > 0) {
                    $join->where('nk.id_jadwal', '=', $selectedJadwalId);
                }

                if ($selectedDosenId > 0) {
                    $join->where('nk.id_dosen', '=', $selectedDosenId);
                }
            })
            ->select([
                'sk.id',
                'sk.no_soal',
                'sk.soal',
                'sk.category',
                DB::raw('SUM(CASE WHEN nk.nilai = 1 THEN 1 ELSE 0 END) as count_sts'),
                DB::raw('SUM(CASE WHEN nk.nilai = 2 THEN 1 ELSE 0 END) as count_ts'),
                DB::raw('SUM(CASE WHEN nk.nilai = 3 THEN 1 ELSE 0 END) as count_s'),
                DB::raw('SUM(CASE WHEN nk.nilai = 4 THEN 1 ELSE 0 END) as count_ss'),
                DB::raw('COUNT(nk.id) as total_jawaban'),
                DB::raw('ROUND(AVG(nk.nilai), 2) as rata_nilai'),
            ])
            ->where('sk.id_ta', $idTahun)
            ->groupBy('sk.id', 'sk.no_soal', 'sk.soal', 'sk.category')
            ->orderBy('sk.category')
            ->orderByRaw('CAST(sk.no_soal as UNSIGNED), sk.no_soal')
            ->orderBy('sk.id')
            ->get();

        $rekapGroups = collect(TblSoalKuesioner::categoryOptions())
            ->map(function ($label, $category) use ($rekapList) {
                return [
                    'category' => (int) $category,
                    'label' => $label,
                    'items' => $rekapList->where('category', (int) $category)->values(),
                ];
            })
            ->filter(function ($group) {
                return $group['items']->isNotEmpty();
            })
            ->values();

        $summary = [
            'count_sts' => (int) $rekapList->sum('count_sts'),
            'count_ts' => (int) $rekapList->sum('count_ts'),
            'count_s' => (int) $rekapList->sum('count_s'),
            'count_ss' => (int) $rekapList->sum('count_ss'),
            'total_jawaban' => (int) $rekapList->sum('total_jawaban'),
            'rata_nilai' => 0,
        ];

        if ($summary['total_jawaban'] > 0) {
            $summary['rata_nilai'] = round((
                ($summary['count_sts'] * 1) +
                ($summary['count_ts'] * 2) +
                ($summary['count_s'] * 3) +
                ($summary['count_ss'] * 4)
            ) / $summary['total_jawaban'], 2);
        }

        return [
            'rekapList' => $rekapList,
            'rekapGroups' => $rekapGroups,
            'summary' => $summary,
        ];
    }

    private function getJadwalOptions(MasterTahunAjaran $tahun, int $idTahun): Collection
    {
        $jadwalFromTemp = DB::table('master_jadwal_temp as mjt')
            ->leftJoin('master_mata_kuliah as mmk_temp', 'mmk_temp.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->select([
                DB::raw('mjt.id as id_jadwal'),
                'mjt.id_dosen',
                'mjt.id_dosen2',
                DB::raw('NULLIF(mjt.kode_jadwal, "") as kode_jadwal'),
                'mjt.kode_mata_kuliah',
                DB::raw('COALESCE(NULLIF(mmk_temp.nama_mata_kuliah, ""), NULLIF(mjt.kode_mata_kuliah, "")) as nama_mata_kuliah'),
                DB::raw('NULLIF(mjt.kelas, "") as kelas'),
                DB::raw('NULLIF(mjt.rombel, "") as rombel'),
                DB::raw("CONCAT(COALESCE(NULLIF(mjt.kode_jadwal, ''), CONCAT('Jadwal #', mjt.id)), ' - ', COALESCE(NULLIF(mmk_temp.nama_mata_kuliah, ''), NULLIF(mjt.kode_mata_kuliah, ''), 'Mata Kuliah')) as label_jadwal"),
            ])
            ->where('mjt.id_tahun', $idTahun)
            ->where('mjt.tipe_mhs', (int) ($tahun->tipe_mhs ?? 1))
            ->get();

        $jadwalFromHistory = DB::table('master_jadwal as mj')
            ->leftJoin('master_mata_kuliah as mmk_hist', 'mmk_hist.kode_mata_kuliah', '=', 'mj.kode_mata_kuliah')
            ->select([
                'mj.id_jadwal',
                'mj.id_dosen',
                'mj.id_dosen2',
                DB::raw('NULLIF(mj.kode_jadwal, "") as kode_jadwal'),
                'mj.kode_mata_kuliah',
                DB::raw('COALESCE(NULLIF(mmk_hist.nama_mata_kuliah, ""), NULLIF(mj.kode_mata_kuliah, "")) as nama_mata_kuliah'),
                DB::raw('NULLIF(mj.kelas, "") as kelas'),
                DB::raw('NULLIF(mj.rombel, "") as rombel'),
                DB::raw("CONCAT(COALESCE(NULLIF(mj.kode_jadwal, ''), CONCAT('Jadwal #', mj.id_jadwal)), ' - ', COALESCE(NULLIF(mmk_hist.nama_mata_kuliah, ''), NULLIF(mj.kode_mata_kuliah, ''), 'Mata Kuliah')) as label_jadwal"),
            ])
            ->where('mj.id_tahun', $idTahun)
            ->where('mj.tipe_mhs', (int) ($tahun->tipe_mhs ?? 1))
            ->get();

        return $jadwalFromTemp
            ->concat($jadwalFromHistory)
            ->unique(function ($row) {
                return (int) ($row->id_jadwal ?? 0);
            })
            ->sortBy(function ($row) {
                return (int) ($row->id_jadwal ?? 0);
            })
            ->values();
    }

    private function getDosenOptions(Collection $jadwalOptions, int $selectedJadwalId): Collection
    {
        if ($selectedJadwalId <= 0) {
            return collect();
        }

        $jadwalSelected = $jadwalOptions->first(fn($x) => (int) $x->id_jadwal === $selectedJadwalId);

        if (!$jadwalSelected) {
            return collect();
        }

        $dosenIds = collect([
            (int) ($jadwalSelected->id_dosen ?? 0),
            (int) ($jadwalSelected->id_dosen2 ?? 0),
        ])->filter(fn($id) => $id > 0)->unique()->values();

        if ($dosenIds->isEmpty()) {
            return collect();
        }

        return DB::table('pegawai_biodata as pb')
            ->select([
                'pb.id as id_dosen',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen"),
            ])
            ->whereIn('pb.id', $dosenIds->all())
            ->orderBy('nama_dosen')
            ->get();
    }
}
