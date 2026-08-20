<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IjazahPeriode;
use App\Models\IjazahDokumen;
use App\Models\Mahasiswa;
use App\Models\YudisiumPendaftaran;
use Illuminate\Support\Facades\DB;

class IjazahController extends Controller
{
    private function getCurrentOfficials()
    {
        $struktur = DB::table('struktur_pegawai2')->where('id', 1)->first();
        if (!$struktur)
            return [];

        $npps = [
            'ketua_st' => trim($struktur->ketua_st ?? ''),
            'pembantu_1' => trim($struktur->pembantu_1 ?? ''),
            'prodi_s1' => trim($struktur->prodi_s1 ?? ''),
            'prodi_d3' => trim($struktur->prodi_d3 ?? '')
        ];

        $officials = [];
        foreach ($npps as $key => $npp) {
            if ($npp === '') {
                $officials[$key] = ['nama' => '', 'nip' => ''];
                continue;
            }
            $pegawai = DB::table('pegawai as p')
                ->leftJoin('pegawai_biodata as pb', 'pb.id_pegawai', '=', 'p.id')
                ->select(
                    'pb.nip_pns',
                    DB::raw("TRIM(CONCAT(COALESCE(pb.gelar_depan,''), ' ', COALESCE(pb.nama_lengkap, p.nama, ''), ' ', COALESCE(pb.gelar_belakang,''))) as nama_gelar")
                )
                ->where('p.npp', $npp)
                ->first();

            $officials[$key] = [
                'nama' => $pegawai->nama_gelar ?? '',
                'nip' => $pegawai->nip_pns ?? ''
            ];
        }
        return $officials;
    }

    public function index()
    {
        $data['CurrentPage'] = 'table-datatable-basic';
        $data['title'] = 'Manajemen Ijazah & Transkrip';
        $data['periodes'] = IjazahPeriode::orderBy('tanggal_wisuda', 'desc')->get();
        $data['current_officials'] = $this->getCurrentOfficials();

        return view('admin.ijazah.index', $data);
    }

    public function storePeriode(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required',
            'tanggal_wisuda' => 'required|date'
        ]);

        $data = $request->all();

        // Auto-fill dari master pejabat jika masih kosong dari input form
        $officials = $this->getCurrentOfficials();

        $data['nama_ketua'] = $data['nama_ketua'] ?? ($officials['ketua_st']['nama'] ?? null);
        $data['nip_ketua'] = $data['nip_ketua'] ?? ($officials['ketua_st']['nip'] ?? null);

        $data['nama_puket_1'] = $data['nama_puket_1'] ?? ($officials['pembantu_1']['nama'] ?? null);
        $data['nip_puket_1'] = $data['nip_puket_1'] ?? ($officials['pembantu_1']['nip'] ?? null);

        $data['nama_kaprodi_s1'] = $data['nama_kaprodi_s1'] ?? ($officials['prodi_s1']['nama'] ?? null);
        $data['nip_kaprodi_s1'] = $data['nip_kaprodi_s1'] ?? ($officials['prodi_s1']['nip'] ?? null);

        $data['nama_kaprodi_d3'] = $data['nama_kaprodi_d3'] ?? ($officials['prodi_d3']['nama'] ?? null);
        $data['nip_kaprodi_d3'] = $data['nip_kaprodi_d3'] ?? ($officials['prodi_d3']['nip'] ?? null);

        $periode = IjazahPeriode::create($data);

        return redirect()->back()->with('success', 'Periode Ijazah berhasil ditambahkan.');
    }

    public function updatePeriode(Request $request, $id)
    {
        $periode = IjazahPeriode::findOrFail($id);
        $data = $request->all();

        // Auto-fill dari master pejabat jika direquest kosong
        $officials = $this->getCurrentOfficials();

        if (empty($data['nama_ketua'])) {
            $data['nama_ketua'] = $officials['ketua_st']['nama'] ?? null;
            $data['nip_ketua'] = $officials['ketua_st']['nip'] ?? null;
        }
        if (empty($data['nama_puket_1'])) {
            $data['nama_puket_1'] = $officials['pembantu_1']['nama'] ?? null;
            $data['nip_puket_1'] = $officials['pembantu_1']['nip'] ?? null;
        }
        if (empty($data['nama_kaprodi_s1'])) {
            $data['nama_kaprodi_s1'] = $officials['prodi_s1']['nama'] ?? null;
            $data['nip_kaprodi_s1'] = $officials['prodi_s1']['nip'] ?? null;
        }
        if (empty($data['nama_kaprodi_d3'])) {
            $data['nama_kaprodi_d3'] = $officials['prodi_d3']['nama'] ?? null;
            $data['nip_kaprodi_d3'] = $officials['prodi_d3']['nip'] ?? null;
        }

        $periode->update($data);
        return redirect()->back()->with('success', 'Setup pejabat penandatangan berhasil diperbarui.');
    }

    public function destroyPeriode($id)
    {
        $periode = IjazahPeriode::findOrFail($id);
        $periode->delete();
        return redirect()->back()->with('success', 'Periode berhasil dihapus.');
    }

    public function showPeriode($id)
    {
        $data['CurrentPage'] = 'table-datatable-basic';
        $data['periode'] = IjazahPeriode::findOrFail($id);
        $data['title'] = 'Daftar Kelulusan: ' . $data['periode']->nama_periode;

        // Auto Sync from YudisiumPendaftaran if they don't exist yet
        // Asumsi: Semua yg LULUS YUDISIUM bisa di-generate Ijazahnya.
        $lulusYudisium = YudisiumPendaftaran::where('status_pengajuan', 'lulus_yudisium')->get();
        foreach ($lulusYudisium as $yud) {
            $exists = IjazahDokumen::where('id_mahasiswa', $yud->id_mahasiswa)->exists();
            if (!$exists) {
                IjazahDokumen::create([
                    'id_mahasiswa' => $yud->id_mahasiswa,
                    'id_periode' => $id,
                    'kategori_kelulusan' => 'Memuaskan'
                ]);
            }
        }

        $data['dokumens'] = IjazahDokumen::where('id_periode', $id)->with('mahasiswa')->get();

        return view('admin.ijazah.detail_periode', $data);
    }

    public function editPenomoran($id)
    {
        $data['CurrentPage'] = 'table-datatable-basic';
        $data['title'] = 'Update Penomoran Dokumen';
        $data['dokumen'] = IjazahDokumen::with('mahasiswa')->findOrFail($id);

        return view('admin.ijazah.penomoran', $data);
    }

    public function updatePenomoran(Request $request, $id)
    {
        $request->validate([
            'no_ijazah' => 'nullable|string',
            'no_transkrip' => 'nullable|string',
            'pin_dikti' => 'nullable|string',
            'kategori_kelulusan' => 'required|in:Memuaskan,Sangat Memuaskan,Cumlaude',
            'tanggal_terbit' => 'nullable|date',
        ]);

        $dokumen = IjazahDokumen::findOrFail($id);
        $dokumen->update($request->all());

        return redirect()->route('admin.ijazah.show', $dokumen->id_periode)->with('success', 'Penomoran berhasil diupdate.');
    }

    public function cetakIjazah($id)
    {
        $data['dokumen'] = IjazahDokumen::with(['mahasiswa.programStudi', 'periode'])->findOrFail($id);
        return view('admin.ijazah.cetak_ijazah', $data);
    }

    private function getNilaiAngka($huruf)
    {
        $huruf = trim(strtoupper($huruf));
        $map = [
            'A' => 4,
            'AB' => 3.5,
            'B' => 3,
            'BC' => 2.5,
            'C' => 2,
            'D' => 1,
            'E' => 0
        ];
        return isset($map[$huruf]) ? $map[$huruf] : 0;
    }

    public function cetakTranskrip($id, $jenis)
    {
        $dokumen = IjazahDokumen::with(['mahasiswa.programStudi', 'periode'])->findOrFail($id);

        // Ambil riwayat KHS
        // Di sistem umum Siakad: tbl_yudisium_nilai atau query custom.
        // Kita gunakan table tbl_yudisium_nilai as fallback jika standar KHS tidak menentu.
        // Tapi instruction asks for: logika Best Grade.
        $riwayat = DB::table('master_nilai as n')
            ->join('master_jadwal as j', 'n.id_jadwal', '=', 'j.id_jadwal')
            ->join('master_mata_kuliah as mk', 'j.kode_mata_kuliah', '=', 'mk.kode_mata_kuliah')
            ->where('n.nim', $dokumen->mahasiswa->nim)
            ->whereNotNull('n.nhuruf')
            ->where('n.nhuruf', '!=', '')
            ->select('mk.kode_mata_kuliah', 'mk.nama_mata_kuliah as nama_mk', 'mk.nama_mata_kuliah_eng as nama_eng', 'mk.jumlah_sks as sks', 'n.nhuruf as nilai_huruf')
            ->get();

        // Jika dari riwayat KHS kosong (sistem lama), cari di tbl_yudisium_nilai
        if ($riwayat->count() == 0) {
            $riwayat = DB::table('tbl_yudisium_nilai')
                ->where('nim', $dokumen->mahasiswa->nim)
                ->whereNotNull('nilai_huruf')
                ->where('nilai_huruf', '!=', '')
                ->select(DB::raw("kode_matkul as kode_mata_kuliah, nama as nama_mk, '' as nama_eng, 2 as sks, nilai_huruf")) // Fallback SKS assumed
                ->get();
        }

        // Logic Best Grade
        $grouped = [];
        foreach ($riwayat as $r) {
            $kode = trim($r->kode_mata_kuliah);
            $huruf = trim(strtoupper($r->nilai_huruf));
            $angka = $this->getNilaiAngka($huruf);
            $sks = (int) $r->sks;

            if (!isset($grouped[$kode])) {
                $grouped[$kode] = [
                    'kode' => $kode,
                    'nama_mk' => $r->nama_mk,
                    'nama_eng' => $r->nama_eng ?? '',
                    'sks' => $sks,
                    'nilai_huruf' => $huruf,
                    'nilai_angka' => $angka
                ];
            } else {
                if ($angka > $grouped[$kode]['nilai_angka']) {
                    $grouped[$kode]['nilai_huruf'] = $huruf;
                    $grouped[$kode]['nilai_angka'] = $angka;
                }
            }
        }

        // Group into Custom Categories (A to E) per PDF reference.
        // As a heuristic for demonstration without a specific category column in `master_mata_kuliah`:
        $categories = [
            'A' => ['name' => 'MATA KULIAH PENGEMBANGAN KEPRIBADIAN / PERSONALITY DEVELOPMENT', 'items' => []],
            'B' => ['name' => 'MATA KULIAH KEILMUAN DAN KETERAMPILAN / SCIENCE AND SKILL', 'items' => []],
            'C' => ['name' => 'MATA KULIAH KEAHLIAN BERKARYA / WORKING EXPERTISE', 'items' => []],
            'D' => ['name' => 'MATA KULIAH PERILAKU BERKARYA / WORKING BEHAVIOR', 'items' => []],
            'E' => ['name' => 'MATA KULIAH BERKEHIDUPAN BERMASYARAKAT / SOCIAL LIFE', 'items' => []],
        ];

        $totalSKS = 0;
        $totalMutu = 0;

        foreach ($grouped as $mk) {
            $totalSKS += $mk['sks'];
            $totalMutu += ($mk['sks'] * $mk['nilai_angka']);

            // Heuristic routing for categories since it is a static mockup standard
            $nameStr = strtolower($mk['nama_mk']);
            if (strpos($nameStr, 'pancasila') !== false || strpos($nameStr, 'agama') !== false || strpos($nameStr, 'kewarganegaraan') !== false || strpos($nameStr, 'bahasa') !== false) {
                $categories['A']['items'][] = $mk;
            } elseif (strpos($nameStr, 'skripsi') !== false || strpos($nameStr, 'karya tulis') !== false || strpos($nameStr, 'pkl') !== false || strpos($nameStr, 'proposal') !== false) {
                $categories['E']['items'][] = $mk;
            } elseif (strpos($nameStr, 'undang') !== false || strpos($nameStr, 'etika') !== false || strpos($nameStr, 'manajemen') !== false || strpos($nameStr, 'entrepreneur') !== false) {
                $categories['D']['items'][] = $mk;
            } elseif (strpos($nameStr, 'praktikum') !== false || strpos($nameStr, 'analisis') !== false || strpos($nameStr, 'formulasi') !== false || strpos($nameStr, 'farmasetika') !== false) {
                $categories['C']['items'][] = $mk;
            } else {
                $categories['B']['items'][] = $mk; // Fallback
            }
        }

        // Sorting items inside safely
        foreach ($categories as $letter => $cat) {
            usort($categories[$letter]['items'], function ($a, $b) {
                return strcmp($a['kode'], $b['kode']);
            });
        }

        $ipk = $totalSKS > 0 ? ($totalMutu / $totalSKS) : 0;

        $data = [
            'dokumen' => $dokumen,
            'categories' => $categories,
            'totalSKS' => $totalSKS,
            'ipk' => number_format($ipk, 2),
            'kaprodi' => ($dokumen->mahasiswa->id_program_studi == 1) ? $dokumen->periode->nama_kaprodi_d3 : $dokumen->periode->nama_kaprodi_s1,
            'nip_kaprodi' => ($dokumen->mahasiswa->id_program_studi == 1) ? $dokumen->periode->nip_kaprodi_d3 : $dokumen->periode->nip_kaprodi_s1,
            'ketua' => $dokumen->periode->nama_ketua,
            'nip_ketua' => $dokumen->periode->nip_ketua,
            'puket_1' => $dokumen->periode->nama_puket_1,
            'nip_puket_1' => $dokumen->periode->nip_puket_1,
        ];

        if ($jenis == 'depan')
            return view('admin.ijazah.cetak_transkrip_depan', $data);
        return view('admin.ijazah.cetak_transkrip_belakang', $data);
    }
}
