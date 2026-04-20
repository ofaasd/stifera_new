<?php

namespace App\Http\Controllers;

use App\Models\MasterPertemuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PegawaiPresensiController extends Controller
{
    /**
     * Resolve pegawai_biodata.id dari id_pegawai (id di tabel pegawai).
     * master_jadwal_temp.id_dosen / id_dosen2 merujuk ke pegawai_biodata.id.
     */
    private function getBiodataId(int $idPegawai): ?int
    {
        $row = DB::table('pegawai_biodata')
            ->where('id_pegawai', $idPegawai)
            ->select('id')
            ->first();

        return $row ? (int) $row->id : null;
    }

    /**
     * Daftar jadwal yang diampu oleh dosen yang sedang login
     * (sebagai pengampu 1 atau pengampu 2).
     */
    public function index()
    {
        $pegawai   = Auth::guard('pegawai')->user();
        if (!$pegawai) abort(403);

        $biodataId = $this->getBiodataId((int) $pegawai->id);

        $temu = DB::table('master_jadwal_temp as mjt')
            ->select(
                'mjt.*',
                'mmk.nama_mata_kuliah',
                DB::raw("CONCAT(COALESCE(pb1.gelar_depan,''), ' ', COALESCE(pb1.nama_lengkap,''), ' ', COALESCE(pb1.gelar_belakang,'')) as nama_dosen"),
                DB::raw("CONCAT(COALESCE(pb2.gelar_depan,''), ' ', COALESCE(pb2.nama_lengkap,''), ' ', COALESCE(pb2.gelar_belakang,'')) as nama_dosen2")
            )
            ->join('master_mata_kuliah as mmk', 'mjt.kode_mata_kuliah', '=', 'mmk.kode_mata_kuliah')
            ->leftJoin('pegawai_biodata as pb1', 'mjt.id_dosen', '=', 'pb1.id')
            ->leftJoin('pegawai_biodata as pb2', 'mjt.id_dosen2', '=', 'pb2.id')
            ->where(function ($q) use ($biodataId) {
                $q->where('mjt.id_dosen', $biodataId)
                  ->orWhere('mjt.id_dosen2', $biodataId);
            })
            ->orderBy('mjt.hari')
            ->orderBy('mjt.sesi')
            ->get();

        return view('pegawai.presensi.index', [
            'title'       => 'Presensi',
            'CurrentPage' => 'content',
            'temu'        => $temu,
            'no'          => 1,
        ]);
    }

    /**
     * Daftar pertemuan (tanggal) dari satu jadwal.
     */
    public function detail(string $id)
    {
        $pegawai   = Auth::guard('pegawai')->user();
        if (!$pegawai) abort(403);

        $biodataId = $this->getBiodataId((int) $pegawai->id);

        // Validasi: jadwal harus milik dosen ini
        $jadwal = DB::table('master_jadwal_temp')
            ->where('id', (int) $id)
            ->where(function ($q) use ($biodataId) {
                $q->where('id_dosen', $biodataId)
                  ->orWhere('id_dosen2', $biodataId);
            })
            ->first();

        if (!$jadwal) {
            return redirect('dosen/presensi')->with('error', 'Data jadwal tidak ditemukan atau bukan milik Anda.');
        }

        $temu = MasterPertemuan::where('id_jadwal', (int) $id)->orderBy('id_pertemuan')->get();

        return view('pegawai.presensi.detail_presensi', [
            'title'       => 'Detail Presensi',
            'CurrentPage' => 'content',
            'temu'        => $temu,
            'id_jadwal'   => $id,
            'no'          => 1,
        ]);
    }

    /**
     * Form input presensi per pertemuan.
     */
    public function create(string $id)
    {
        $pegawai   = Auth::guard('pegawai')->user();
        if (!$pegawai) abort(403);

        $biodataId = $this->getBiodataId((int) $pegawai->id);

        $id_pertemuan_row = DB::table('master_pertemuan')->where('id', (int) $id)->first();

        if ($id_pertemuan_row) {
            // Validasi: jadwal harus milik dosen ini
            $jadwal = DB::table('master_jadwal_temp')
                ->where('id', (int) $id_pertemuan_row->id_jadwal)
                ->where(function ($q) use ($biodataId) {
                    $q->where('id_dosen', $biodataId)
                      ->orWhere('id_dosen2', $biodataId);
                })
                ->first();

            if (!$jadwal) {
                return redirect('dosen/presensi')->with('error', 'Akses ditolak. Pertemuan ini bukan milik Anda.');
            }

            $mhs = DB::table('master_krs_temp')
                ->select('master_krs_temp.*', 'mahasiswa.nama')
                ->join('mahasiswa', 'master_krs_temp.nim', '=', 'mahasiswa.nim')
                ->where('master_krs_temp.id_jadwal', $id_pertemuan_row->id_jadwal)
                ->get();

            $tgl_pertemuan = $id_pertemuan_row->tgl_pertemuan;
            $jadwal_id     = $id_pertemuan_row->id_jadwal;
        } else {
            $mhs           = collect();
            $tgl_pertemuan = null;
            $jadwal_id     = null;
        }

        $memos = DB::table('tbl_memo')->where('id_pertemuan', (int) $id)->first();

        return view('pegawai.presensi.input_presensi', [
            'title'         => 'Input Presensi',
            'CurrentPage'   => 'content',
            'mhs'           => $mhs,
            'tgl_pertemuan' => $tgl_pertemuan,
            'jadwal_id'     => $jadwal_id,
            'memos'         => $memos,
            'id_pertemuan'  => $id,
            'no'            => 1,
        ]);
    }

    /**
     * Simpan presensi dan memo pertemuan.
     */
    public function store(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai) abort(403);

        $memo          = $request->input('memo');
        $sub           = $request->input('sub');
        $id_pertemuan  = $request->input('id_pertemuan');
        $id_jadwal_arr = $request->input('id_jadwal', []);
        $nim_arr       = $request->input('nim', []);
        $tgl_arr       = $request->input('tgl', []);
        $status_arr    = $request->input('status', []);

        if (count($nim_arr) === 0) {
            return redirect('dosen/presensi/input/' . $id_pertemuan)
                ->with('error', 'Tidak ada data mahasiswa untuk disimpan.');
        }

        $mhs_hadir       = 0;
        $mhs_tidak_hadir = 0;

        try {
            DB::beginTransaction();

            for ($i = 0; $i < count($nim_arr); $i++) {
                $status = (int) $status_arr[$i];
                $status === 1 ? $mhs_hadir++ : $mhs_tidak_hadir++;

                DB::table('master_presensi')->updateOrInsert(
                    [
                        'nim'           => $nim_arr[$i],
                        'id_jadwal'     => $id_jadwal_arr[$i],
                        'tgl_pertemuan' => $tgl_arr[$i],
                    ],
                    [
                        'status' => $status,
                    ]
                );
            }

            DB::table('tbl_memo')->updateOrInsert(
                ['id_pertemuan' => $id_pertemuan],
                [
                    'memo'            => $memo,
                    'sub'             => $sub,
                    'mhs_hadir'       => $mhs_hadir,
                    'mhs_tidak_hadir' => $mhs_tidak_hadir,
                ]
            );

            DB::commit();

            return redirect('dosen/presensi/input/' . $id_pertemuan)
                ->with('presensi', 'Presensi berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect('dosen/presensi/input/' . $id_pertemuan)
                ->with('error', 'Gagal menyimpan presensi: ' . $e->getMessage());
        }
    }
}
