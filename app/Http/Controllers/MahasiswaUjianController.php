<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class MahasiswaUjianController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.login');
        }

        $tahunAktif = $this->getTahunAktifByTipe((int) ($mahasiswa->tipe_mhs ?? 0));

        $ujianRows = collect();
        $isUtsDiizinkan = false;
        $isUasDiizinkan = false;
        $mahasiswaProfil = $this->getMahasiswaProfil((int) ($mahasiswa->id ?? 0));

        if ($tahunAktif) {
            $izinUjian = DB::table('master_keuangan_mhs')
                ->where('id_mahasiswa', (int) ($mahasiswa->id ?? 0))
                ->where('id_tahun_ajaran', (int) $tahunAktif->id)
                ->select('uts', 'uas')
                ->first();

            $isUtsDiizinkan = (int) ($izinUjian->uts ?? 0) === 1;
            $isUasDiizinkan = (int) ($izinUjian->uas ?? 0) === 1;

            $ujianRows = $this->getJadwalUjianMahasiswa($mahasiswa->nim, (int) $tahunAktif->id);
        }

        return view('mahasiswa.ujian', [
            'title' => 'Kartu Ujian',
            'CurrentPage' => 'content',
            'mahasiswa' => $mahasiswa,
            'mahasiswaProfil' => $mahasiswaProfil,
            'tahunAktif' => $tahunAktif,
            'jenisTA' => $this->formatJenisSemester((int) ($tahunAktif->jenis ?? 0)),
            'isUtsDiizinkan' => $isUtsDiizinkan,
            'isUasDiizinkan' => $isUasDiizinkan,
            'ujianRows' => $ujianRows,
            'utsRows' => $isUtsDiizinkan ? $this->mapUjianByJenis($ujianRows, 'uts') : collect(),
            'uasRows' => $isUasDiizinkan ? $this->mapUjianByJenis($ujianRows, 'uas') : collect(),
        ]);
    }

    public function downloadUts()
    {
        return $this->downloadKartuUjian('uts');
    }

    public function downloadUas()
    {
        return $this->downloadKartuUjian('uas');
    }

    private function downloadKartuUjian(string $jenis)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        if (!$mahasiswa) {
            abort(403);
        }

        $tahunAktif = $this->getTahunAktifByTipe((int) ($mahasiswa->tipe_mhs ?? 0));
        if (!$tahunAktif) {
            return redirect()->route('mahasiswa.ujian.index')->with('error', 'Tahun ajaran aktif tidak ditemukan.');
        }

        $izinUjian = DB::table('master_keuangan_mhs')
            ->where('id_mahasiswa', (int) ($mahasiswa->id ?? 0))
            ->where('id_tahun_ajaran', (int) $tahunAktif->id)
            ->select('uts', 'uas')
            ->first();

        $isUtsDiizinkan = (int) ($izinUjian->uts ?? 0) === 1;
        $isUasDiizinkan = (int) ($izinUjian->uas ?? 0) === 1;

        if ($jenis === 'uts' && !$isUtsDiizinkan) {
            return redirect()->route('mahasiswa.ujian.index')->with('error', 'Download kartu UTS belum diizinkan oleh admin keuangan.');
        }

        if ($jenis === 'uas' && !$isUasDiizinkan) {
            return redirect()->route('mahasiswa.ujian.index')->with('error', 'Download kartu UAS belum diizinkan oleh admin keuangan.');
        }

        $ujianRows = $this->getJadwalUjianMahasiswa($mahasiswa->nim, (int) $tahunAktif->id);
        if ($ujianRows->isEmpty()) {
            return redirect()->route('mahasiswa.ujian.index')->with('error', 'Data kartu ujian belum tersedia.');
        }

        $mahasiswaProfil = $this->getMahasiswaProfil((int) ($mahasiswa->id ?? 0));
        $html = view('mahasiswa.ujian_pdf', [
            'mahasiswa' => $mahasiswa,
            'mahasiswaProfil' => $mahasiswaProfil,
            'tahunAktif' => $tahunAktif,
            'jenisTA' => $this->formatJenisSemester((int) ($tahunAktif->jenis ?? 0)),
            'ujianRows' => $ujianRows,
            'jenisKartu' => $jenis,
        ])->render();
        
        $mpdf = new Mpdf([
            'format' => 'A4',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 8,
        ]);

        $mpdf->WriteHTML($html);
        $filename = 'kartu_ujian_' . $jenis . '_' . ($mahasiswa->nim ?? 'mahasiswa') . '.pdf';

        return response($mpdf->Output($filename, 'D'))
            ->header('Content-Type', 'application/pdf');
    }

    private function getJadwalUjianMahasiswa(string $nim, int $idTahun): Collection
    {
        return DB::table('master_krs_temp as mk')
            ->join('master_jadwal_temp as mjt', function ($join) {
                $join->on('mjt.id', '=', 'mk.id_jadwal')
                    ->on('mjt.id_tahun', '=', 'mk.id_tahun');
            })
            ->leftJoin('master_mata_kuliah as mmk', 'mmk.kode_mata_kuliah', '=', 'mjt.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb', 'pb.id', '=', 'mjt.id_dosen')
            ->leftJoin('tbl_tempat_ujian as ttu', function ($join) {
                $join->on('ttu.id_jadwal', '=', 'mjt.id')
                    ->on('ttu.ta', '=', 'mjt.id_tahun')
                    ->on('ttu.nim', '=', 'mk.nim');
            })
            ->leftJoin('tbl_jadwal_ujian as tju', function ($join) {
                $join->on('tju.id_jadwal', '=', 'mjt.id')
                    ->on('tju.ta', '=', 'mjt.id_tahun');
            })
            ->leftJoin('master_jam as jam_uts_t', 'jam_uts_t.id', '=', 'tju.id_jam_uts_t')
            ->leftJoin('master_jam as jam_uts_p', 'jam_uts_p.id', '=', 'tju.id_jam_uts_p')
            ->leftJoin('master_jam as jam_uas_t', 'jam_uas_t.id', '=', 'tju.id_jam_uas_t')
            ->leftJoin('master_jam as jam_uas_p', 'jam_uas_p.id', '=', 'tju.id_jam_uas_p')
            ->select(
                'mk.id_jadwal',
                'mk.id_tahun',
                'mjt.kode_mata_kuliah',
                'mmk.nama_mata_kuliah',
                'mmk.jumlah_sks',
                DB::raw('COALESCE(ttu.ruang, mjt.ruang) as ruang'),
                'mjt.kelas',
                'mjt.rombel',
                'ttu.no_kursi',
                DB::raw("CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap,''), ' ', COALESCE(pb.gelar_belakang,'')) as nama_dosen"),
                'tju.tanggal_uts_t',
                'tju.tanggal_uts_p',
                'tju.tanggal_uas_t',
                'tju.tanggal_uas_p',
                'jam_uts_t.nama_sesi as nama_sesi_uts_t',
                'jam_uts_t.mulai as mulai_uts_t',
                'jam_uts_t.selesai as selesai_uts_t',
                'jam_uts_p.nama_sesi as nama_sesi_uts_p',
                'jam_uts_p.mulai as mulai_uts_p',
                'jam_uts_p.selesai as selesai_uts_p',
                'jam_uas_t.nama_sesi as nama_sesi_uas_t',
                'jam_uas_t.mulai as mulai_uas_t',
                'jam_uas_t.selesai as selesai_uas_t',
                'jam_uas_p.nama_sesi as nama_sesi_uas_p',
                'jam_uas_p.mulai as mulai_uas_p',
                'jam_uas_p.selesai as selesai_uas_p'
            )
            ->where('mk.nim', $nim)
            ->where('mk.id_tahun', $idTahun)
            ->orderBy('mmk.nama_mata_kuliah')
            ->get();
    }

    private function getMahasiswaProfil(int $idMahasiswa): ?object
    {
        return DB::table('mahasiswa as m')
            ->leftJoin('program_studi as ps', 'ps.id', '=', 'm.id_program_studi')
            ->leftJoin('master_program_studi as mps', 'mps.id', '=', 'm.id_program_studi')
            ->leftJoin('fakultas as f', 'f.id', '=', 'ps.fakultas')
            ->leftJoin('pegawai_biodata as pbw', 'pbw.id_pegawai', '=', 'm.id_dsn_wali')
            ->select(
                'm.id',
                'm.nim',
                'm.nama',
                'm.email',
                DB::raw("COALESCE(NULLIF(CONCAT(COALESCE(ps.jenjang,''), ' ', COALESCE(ps.nama_jurusan,'')), ' '), mps.nama_jurusan, '-') as nama_program_studi"),
                DB::raw("COALESCE(f.nama_fakultas, '-') as nama_fakultas"),
                DB::raw("CONCAT(COALESCE(pbw.gelar_depan,''), ' ', COALESCE(pbw.nama_lengkap,''), ' ', COALESCE(pbw.gelar_belakang,'')) as dosen_wali"),
                DB::raw("COALESCE(pbw.email1, '-') as email_dosen_wali")
            )
            ->where('m.id', $idMahasiswa)
            ->first();
    }

    private function mapUjianByJenis(Collection $rows, string $jenis): Collection
    {
        return $rows->map(function ($row) use ($jenis) {
            if ($jenis === 'uts') {
                return (object) [
                    'kode_mata_kuliah' => $row->kode_mata_kuliah,
                    'nama_mata_kuliah' => $row->nama_mata_kuliah,
                    'jumlah_sks' => $row->jumlah_sks,
                    'ruang' => $row->ruang,
                    'kelas' => $row->kelas,
                    'rombel' => $row->rombel,
                    'nama_dosen' => $row->nama_dosen,
                    'tanggal_teori' => $row->tanggal_uts_t,
                    'tanggal_praktik' => $row->tanggal_uts_p,
                    'sesi_teori' => $row->nama_sesi_uts_t,
                    'mulai_teori' => $row->mulai_uts_t,
                    'selesai_teori' => $row->selesai_uts_t,
                    'sesi_praktik' => $row->nama_sesi_uts_p,
                    'mulai_praktik' => $row->mulai_uts_p,
                    'selesai_praktik' => $row->selesai_uts_p,
                ];
            }

            return (object) [
                'kode_mata_kuliah' => $row->kode_mata_kuliah,
                'nama_mata_kuliah' => $row->nama_mata_kuliah,
                'jumlah_sks' => $row->jumlah_sks,
                'ruang' => $row->ruang,
                'kelas' => $row->kelas,
                'rombel' => $row->rombel,
                'nama_dosen' => $row->nama_dosen,
                'tanggal_teori' => $row->tanggal_uas_t,
                'tanggal_praktik' => $row->tanggal_uas_p,
                'sesi_teori' => $row->nama_sesi_uas_t,
                'mulai_teori' => $row->mulai_uas_t,
                'selesai_teori' => $row->selesai_uas_t,
                'sesi_praktik' => $row->nama_sesi_uas_p,
                'mulai_praktik' => $row->mulai_uas_p,
                'selesai_praktik' => $row->selesai_uas_p,
            ];
        });
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
}
