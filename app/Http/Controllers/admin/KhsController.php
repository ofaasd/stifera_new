<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class KhsController extends Controller
{
    //
    public function index(){
        $query['title'] = "Kartu Hasil Studi (KHS) Mahasiswa";
        $query['CurrentPage'] = 'content';
        $query['data'] = Mahasiswa::all();  
        $query['status'] = [1=>'Aktif', 'Cuti', 'keluar', 'Lulus', 'Meninggal', 'DO'];
        $query['no'] = 1;
        return view('admin.khs.index', $query);
    }
    public function show(String $id){
        $query['title'] = "Kartu Hasil Studi (KHS) Mahasiswa";
        $query['CurrentPage'] = 'content';
        
        // 1. Ambil data mahasiswa
        // Menggunakan first() menggantikan row()
        $mahasiswa = DB::table('mahasiswa')->find($id);
        $nim = $mahasiswa->nim;

        // Proteksi: Jika mahasiswa tidak ditemukan, kembalikan ke halaman sebelumnya
        if (!$mahasiswa) {
            return redirect()->back()->with('error', 'Mahasiswa dengan NIM tersebut tidak ditemukan.');
        }

        // 2. Ambil data tahun ajaran aktif berdasarkan tipe mahasiswa
        $ta = DB::table('master_tahun_ajaran')
            ->where('is_aktif', 1)
            ->where('tipe_mhs', $mahasiswa->tipe_mhs)
            ->first();

        // Proteksi: Jika tidak ada tahun ajaran aktif
        if (!$ta) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif untuk tipe mahasiswa ini.');
        }

        // 3. Menentukan string jenis semester
        $jenis = '';
        if ($ta->jenis == 1) {
            $jenis = 'Ganjil';
        } elseif ($ta->jenis == 2) {
            $jenis = 'Genap';
        } elseif ($ta->jenis == 3) {
            $jenis = 'Antara Ganjil Genap';
        } elseif ($ta->jenis == 4) {
            $jenis = 'Antara Genap Ganjil';
        }

        // 4. Siapkan array data KHS
        $query['ta'] = $ta->awal . ' - ' . $ta->akhir . ' ' . $jenis;
        $query['nim'] = $nim;
        
        // Perhitungan tahun angkatan
        $angkatan = substr($nim, 2, 2);
        $tahun_angkatan = "20" . $angkatan;

        // Set Session (Menggantikan $this->session->set_userdata)
        session(['nim' => $nim]);

        // 5. Query KHS (Menggunakan Query Builder)
        $query['khs'] = DB::table('master_nilai as mn')
            ->select(
                'mn.*',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen"),
                'mjt.*',
                'mmk.nama_mata_kuliah as mata_kuliah',
                'mmk.jumlah_sks'
            )
            ->leftJoin('master_jadwal_temp as mjt', 'mjt.id', '=', 'mn.id_jadwal')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mjt.id_dosen')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->where('mn.nim', $nim)
            ->where('mn.id_tahun', $ta->id)
            ->get(); // Menggantikan result()

        // 6. Query History Tahun Ajaran Pertama (<= 2023)
        $query['ta_history'] = DB::table('master_tahun_ajaran')
            ->where('id', '<>', $ta->id)
            ->where('awal', '>=', $tahun_angkatan)
            ->where('awal', '<=', 2023)
            ->where('is_delete', '0') // Asumsi kolom is_delete bertipe string/enum sesuai DB Anda
            ->orderByDesc('id')
            ->get();

        // 7. Query History Tahun Ajaran Kedua (> 2023)
        if ($tahun_angkatan > 2023) {
            $query['ta_history2'] = DB::table('master_tahun_ajaran')
                ->where('id', '<>', $ta->id)
                ->where('awal', '>', $tahun_angkatan)
                ->where('tipe_mhs', $mahasiswa->tipe_mhs)
                ->where('is_delete', '0')
                ->orderByDesc('id')
                ->get();
        } else {
            $query['ta_history2'] = DB::table('master_tahun_ajaran')
                ->where('id', '<>', $ta->id)
                ->where('awal', '>', 2023)
                ->where('tipe_mhs', $mahasiswa->tipe_mhs)
                ->where('is_delete', '0')
                ->orderByDesc('id')
                ->get();
        }
        $query['krs_history'] = [];
        foreach($query['ta_history2'] as $ta_history){
            $ta_id = $ta_history->id;
            $jenis = '';
            
            if ($ta_history->jenis == 1) $jenis = 'Ganjil';
            elseif ($ta_history->jenis == 2) $jenis = 'Genap';
            elseif ($ta_history->jenis == 3) $jenis = 'Antara Ganjil Genap';
            elseif ($ta_history->jenis == 4) $jenis = 'Antara Genap Ganjil';            
            
            // CATATAN PENTING: Memanggil fungsi model di dalam view adalah bad practice (MVC violation).
            // Idealnya, krs_history dikirim dari Controller di dalam loop ta_history2.
            // Tapi untuk menjaga kode ini tetap jalan, saya panggil via service container:
            $query['krs_history'][$ta_history->id] = DB::table('master_nilai')
                    ->select(
                        'master_nilai.*', 
                        DB::raw("CONCAT(COALESCE(pegawai_biodata.gelar_depan, ''), ' ', COALESCE(pegawai_biodata.nama_lengkap, ''), ' ', COALESCE(pegawai_biodata.gelar_belakang, '')) as nama_dosen"), 
                        'master_jadwal.*',
                        'master_mata_kuliah.nama_mata_kuliah as mata_kuliah',
                        'master_mata_kuliah.jumlah_sks'
                    )
                    ->leftJoin('master_jadwal', 'master_jadwal.id_jadwal', '=', 'master_nilai.id_jadwal')
                    ->leftJoin('pegawai_biodata', 'pegawai_biodata.id', '=', 'master_jadwal.id_dosen')
                    ->leftJoin('master_mata_kuliah', 'master_mata_kuliah.kode_mata_kuliah', '=', 'master_jadwal.kode_mata_kuliah')
                    ->where('master_nilai.nim', $nim)
                    ->where('master_nilai.id_tahun', $ta_history->id)
                    ->where('master_jadwal.id_tahun', $ta_history->id)
                    ->get();
        }
        foreach($query['ta_history'] as $ta_history){
            $ta_id = $ta_history->id;
            $jenis = '';
            
            if ($ta_history->jenis == 1) $jenis = 'Ganjil';
            elseif ($ta_history->jenis == 2) $jenis = 'Genap';
            elseif ($ta_history->jenis == 3) $jenis = 'Antara Ganjil Genap';
            elseif ($ta_history->jenis == 4) $jenis = 'Antara Genap Ganjil';            
            
            // CATATAN PENTING: Memanggil fungsi model di dalam view adalah bad practice (MVC violation).
            // Idealnya, krs_history dikirim dari Controller di dalam loop ta_history2.
            // Tapi untuk menjaga kode ini tetap jalan, saya panggil via service container:
            $query['krs_history'][$ta_history->id] = DB::table('master_nilai')
                    ->select(
                        'master_nilai.*', 
                        DB::raw("CONCAT(COALESCE(pegawai_biodata.gelar_depan, ''), ' ', COALESCE(pegawai_biodata.nama_lengkap, ''), ' ', COALESCE(pegawai_biodata.gelar_belakang, '')) as nama_dosen"), 
                        'master_jadwal.*',
                        'master_mata_kuliah.nama_mata_kuliah as mata_kuliah',
                        'master_mata_kuliah.jumlah_sks'
                    )
                    ->leftJoin('master_jadwal', 'master_jadwal.id_jadwal', '=', 'master_nilai.id_jadwal')
                    ->leftJoin('pegawai_biodata', 'pegawai_biodata.id', '=', 'master_jadwal.id_dosen')
                    ->leftJoin('master_mata_kuliah', 'master_mata_kuliah.kode_mata_kuliah', '=', 'master_jadwal.kode_mata_kuliah')
                    ->where('master_nilai.nim', $nim)
                    ->where('master_nilai.id_tahun', $ta_history->id)
                    ->where('master_jadwal.id_tahun', $ta_history->id)
                    ->get();
        }
        return view('admin.khs.detail', $query);
    }

    public function cetak_khs()
    {
        $nim = (string) session('nim', '');
        if ($nim === '') {
            return redirect('master/khs/list_mhs')->with('error', 'Session mahasiswa tidak ditemukan. Buka detail KHS mahasiswa terlebih dahulu.');
        }

        $mahasiswa = DB::table('mahasiswa')->where('nim', $nim)->first();
        if (!$mahasiswa) {
            return redirect('master/khs/list_mhs')->with('error', 'Mahasiswa tidak ditemukan.');
        }

        $tahun = DB::table('master_tahun_ajaran')
            ->where('is_aktif', 1)
            ->where('tipe_mhs', (int) ($mahasiswa->tipe_mhs ?? 0))
            ->first();

        if (!$tahun) {
            return redirect()->back()->with('error', 'Tahun ajaran aktif tidak ditemukan.');
        }

        return $this->downloadPdfByTahunNim((int) $tahun->id, $nim);
    }

    public function cetak_khs_history(string $id_tahun_nim)
    {
        $parts = explode('-', $id_tahun_nim, 2);
        $idTahun = (int) ($parts[0] ?? 0);
        $nim = trim((string) ($parts[1] ?? ''));

        if ($idTahun <= 0 || $nim === '') {
            return redirect()->back()->with('error', 'Parameter download KHS tidak valid.');
        }

        return $this->downloadPdfByTahunNim($idTahun, $nim);
    }

    private function downloadPdfByTahunNim(int $idTahun, string $nim)
    {
        $mahasiswa = DB::table('mahasiswa')->where('nim', $nim)->first();
        if (!$mahasiswa) {
            return redirect('master/khs/list_mhs')->with('error', 'Mahasiswa tidak ditemukan.');
        }

        $tahun = DB::table('master_tahun_ajaran')->where('id', $idTahun)->first();
        if (!$tahun) {
            return redirect()->back()->with('error', 'Tahun ajaran tidak ditemukan.');
        }

        $khsRows = $this->getKhsRowsByTahun($nim, $idTahun);
        if ($khsRows->isEmpty()) {
            return redirect()->back()->with('error', 'Data KHS belum tersedia untuk diunduh.');
        }

        $statAktif = $this->calculateStat($khsRows);
        $ipkMahasiswa = $this->calculateIpk($nim, $idTahun);
        $mahasiswaProfil = $this->getMahasiswaProfil((int) ($mahasiswa->id ?? 0));
        $pembantuKetuaAkademik = $this->getPembantuKetuaAkademik();

        $html = view('mahasiswa.khs_pdf', [
            'mahasiswa' => $mahasiswa,
            'mahasiswaProfil' => $mahasiswaProfil,
            'tahunAktif' => $tahun,
            'jenisTA' => $this->formatJenisSemester((int) ($tahun->jenis ?? 0)),
            'khsRows' => $khsRows,
            'statAktif' => $statAktif,
            'ipkMahasiswa' => $ipkMahasiswa,
            'pembantuKetuaAkademik' => $pembantuKetuaAkademik,
        ])->render();

        $mpdf = new Mpdf([
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
        ]);

        $mpdf->WriteHTML($html);
        $filename = 'admin_khs_' . $nim . '_' . $idTahun . '.pdf';

        return response($mpdf->Output($filename, 'D'))
            ->header('Content-Type', 'application/pdf');
    }

    private function getKhsRowsByTahun(string $nim, int $idTahun)
    {
        $jadwalTable = $this->resolveJadwalTable($idTahun);
        $jadwalJoinKey = $jadwalTable === 'master_jadwal_temp' ? 'id' : 'id_jadwal';

        return DB::table('master_nilai as mn')
            ->leftJoin($jadwalTable . ' as mj', function ($join) use ($idTahun, $jadwalJoinKey) {
                $join->on('mj.' . $jadwalJoinKey, '=', 'mn.id_jadwal')
                    ->where('mj.id_tahun', '=', $idTahun);
            })
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mj.id_dosen')
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mj.kode_mata_kuliah')
            ->select(
                'mn.*',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen"),
                'mmk.kode_mata_kuliah',
                'mmk.nama_mata_kuliah as mata_kuliah',
                'mmk.jumlah_sks'
            )
            ->where('mn.nim', $nim)
            ->where('mn.id_tahun', $idTahun)
            ->orderBy('mn.id')
            ->get();
    }

    private function resolveJadwalTable(int $idTahun): string
    {
        $tahunAjaran = DB::table('master_tahun_ajaran')
            ->select('is_aktif')
            ->where('id', $idTahun)
            ->first();

        return (int) ($tahunAjaran->is_aktif ?? 0) === 1 ? 'master_jadwal_temp' : 'master_jadwal';
    }

    private function calculateStat($rows): array
    {
        $totalSks = 0;
        $totalPoint = 0.0;

        foreach ($rows as $row) {
            $hasNilai = (trim((string) ($row->nhuruf ?? '')) !== '')
                || ($row->nakhir !== null && $row->nakhir !== '');
            if (!$hasNilai) {
                continue;
            }

            $sks = (int) ($row->jumlah_sks ?? 0);
            $totalSks += $sks;

            $nilaiHuruf = $this->resolveNilaiHuruf($row->nhuruf ?? null, $row->nakhir ?? null);
            $point = (float) (function_exists('nbobot') ? nbobot($nilaiHuruf) : 0.0);
            $totalPoint += ($point * $sks);
        }

        return [
            'total_sks' => $totalSks,
            'ips' => $totalSks > 0 ? ($totalPoint / $totalSks) : 0.0,
        ];
    }

    private function calculateIpk(string $nim, ?int $excludeIdTahun = null): float
    {
        $tahunIds = DB::table('master_nilai')
            ->where('nim', $nim)
            ->whereNotNull('id_tahun')
            ->distinct()
            ->pluck('id_tahun');

        $totalSks = 0;
        $totalPoint = 0.0;

        foreach ($tahunIds as $idTahun) {
            if ($excludeIdTahun !== null && (int) $idTahun === $excludeIdTahun) {
                continue;
            }

            $rows = $this->getKhsRowsByTahun($nim, (int) $idTahun);
            foreach ($rows as $row) {
                $hasNilai = (trim((string) ($row->nhuruf ?? '')) !== '')
                    || ($row->nakhir !== null && $row->nakhir !== '');
                if (!$hasNilai) {
                    continue;
                }

                $sks = (int) ($row->jumlah_sks ?? 0);
                $nilaiHuruf = $this->resolveNilaiHuruf($row->nhuruf ?? null, $row->nakhir ?? null);
                $point = (float) (function_exists('nbobot') ? nbobot($nilaiHuruf) : 0.0);

                $totalSks += $sks;
                $totalPoint += ($point * $sks);
            }
        }

        return $totalSks > 0 ? ($totalPoint / $totalSks) : 0.0;
    }

    private function resolveNilaiHuruf(?string $nilaiHuruf, $nilaiAkhir = null): string
    {
        $huruf = strtoupper(trim((string) ($nilaiHuruf ?? '')));
        if ($huruf !== '') {
            return $huruf;
        }

        if ($nilaiAkhir !== null && $nilaiAkhir !== '' && function_exists('nmutu')) {
            return (string) nmutu((float) $nilaiAkhir);
        }

        return 'E';
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

    private function getPembantuKetuaAkademik(): ?string
    {
        $row = DB::table('struktur_pegawai2 as sp2')
            ->leftJoin('pegawai as p', 'p.npp', '=', 'sp2.pembantu_1')
            ->leftJoin('pegawai_biodata as pb', 'pb.id_pegawai', '=', 'p.id')
            ->select(
                DB::raw("TRIM(CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap, p.nama, ''), ' ', COALESCE(pb.gelar_belakang,''))) as nama_gelar")
            )
            ->where('sp2.id', 1)
            ->first();

        $nama = trim((string) ($row->nama_gelar ?? ''));
        return $nama !== '' ? $nama : null;
    }
}
