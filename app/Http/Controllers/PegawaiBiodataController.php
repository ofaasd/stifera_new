<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PegawaiBiodataController extends Controller
{
    public function edit()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $query = DB::query()
            ->select(
                'b.*',
                'p.npp',
                'p.id as pegawai_id',
                'b.id as id_biodata',
                'p.nama as nama_pegawai',
                DB::raw('(SELECT jabatan_fungsional_sekarang FROM pegawai_jabatan_fungsional WHERE id_pegawai = p.id ORDER BY id DESC LIMIT 1) as jabatan_fungsional_sekarang')
            )
            ->from('pegawai as p')
            ->leftJoin('pegawai_biodata as b', 'b.id_pegawai', '=', 'p.id')
            ->leftJoin('pegawai_posisi as ps', 'ps.kode', '=', 'b.kd_posisi_pegawai')
            ->leftJoin('pegawai_jenis as j', 'j.id', '=', 'ps.id_jenis_pegawai')
            ->where('p.status', 1)
            ->where('p.id', $pegawai->id)
            ->orderBy('p.nama', 'asc')
            ->groupBy('p.id')
            ->first();

        if (!$query) {
            abort(404, 'Data pegawai tidak ditemukan.');
        }

        $data = $this->buildEditPayload($query);
        $data['title'] = 'Edit Biodata Pegawai';

        return view('pegawai.biodata.edit', $data);
    }

    public function update(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $pegawaiId = (int) $pegawai->id;
        $npp = $request->input('nip');

        try {
            DB::beginTransaction();

            DB::table('pegawai')
                ->where('id', $pegawaiId)
                ->update([
                    'npp' => $npp,
                ]);

            $biodataId = DB::table('pegawai_biodata')
                ->where('id_pegawai', $pegawaiId)
                ->value('id');

            $biodataPayload = [
                'kd_posisi_pegawai' => $request->input('posisi_pegawai'),
                'homebase' => $request->input('homebase'),
                'nama_lengkap' => $request->input('nama_lengkap'),
                'alamat' => $request->input('alamat'),
                'jenis_kelamin' => $request->input('jenis_kelamin'),
                'nidn' => $request->input('nidn'),
                'status_nikah' => $request->input('status_nikah'),
                'status_pegawai' => $request->input('status'),
                'tanggal_lahir' => $this->parseDate($request->input('tanggal_lahir')),
                'tempat_lahir' => $request->input('tempat_lahir'),
                'gelar_depan' => $request->input('gelar_depan'),
                'gelar_belakang' => $request->input('gelar_belakang'),
                'no_ktp' => $request->input('no_ktp'),
                'no_kk' => $request->input('no_kk'),
                'nama_pasangan' => $request->input('nama_pasangan'),
                'tgl_lahir_pasangan' => $this->parseDate($request->input('tgl_lahir_pasangan')),
                'pekerjaan_pasangan' => $request->input('pekerjaan_pasangan'),
                'jumlah_anak' => $request->input('jumlah_anak'),
                'provinsi' => $request->input('provinsi'),
                'kotakab' => $request->input('kotakab'),
                'kecamatan' => $request->input('kecamatan'),
                'kelurahan' => $request->input('kelurahan'),
                'nohp' => $request->input('nohp'),
                'notelp' => $request->input('notelp'),
                'agama' => $request->input('agama'),
                'email1' => $request->input('email1'),
                'no_bpjs_kesehatan' => $request->input('no_bpjs_kesehatan'),
                'no_bpjs_ketenagakerjaan' => $request->input('no_bpjs_ketenagakerjaan'),
            ];

            if ($biodataId) {
                DB::table('pegawai_biodata')
                    ->where('id', $biodataId)
                    ->update($biodataPayload);
            } else {
                $biodataPayload['id_pegawai'] = $pegawaiId;
                DB::table('pegawai_biodata')->insert($biodataPayload);
            }

            $universitas = $request->input('universitas', []);
            $universitasid = $request->input('universitasid', []);
            $jurusan = $request->input('jurusan', []);
            $jurusanid = $request->input('jurusanid', []);
            $jenjang = $request->input('jenjang', []);
            $statusRiwayat = $request->input('status_riwayat', []);

            foreach ($universitasid as $key => $value) {
                if (empty($value)) {
                    continue;
                }

                $univId = $value;

                if ((string) $value === '999999') {
                    $univId = DB::table('master_universitas')->insertGetId([
                        'nama_universitas' => $universitas[$key] ?? '',
                    ]);
                }

                $prodiId = $jurusanid[$key] ?? null;

                if ((string) $prodiId === '999999') {
                    $prodiId = DB::table('master_program_studi')->insertGetId([
                        'nama_jurusan' => $jurusan[$key] ?? '',
                    ]);
                }

                $riwayatId = (int) ($statusRiwayat[$key] ?? 0);

                if ($riwayatId === 0) {
                    DB::table('pegawai_riwayat_pendidikan')->insert([
                        'id_pegawai' => $pegawaiId,
                        'jenjang' => $jenjang[$key] ?? null,
                        'universitas' => $univId,
                        'jurusan' => $prodiId,
                    ]);
                } else {
                    DB::table('pegawai_riwayat_pendidikan')
                        ->where('id', $riwayatId)
                        ->where('id_pegawai', $pegawaiId)
                        ->update([
                            'jenjang' => $jenjang[$key] ?? null,
                            'universitas' => $univId,
                            'jurusan' => $prodiId,
                        ]);
                }
            }

            DB::commit();

            return redirect()->route('pegawai.biodata.edit')->with('status', 'Biodata pegawai berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }

    public function uploadPhoto(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $pegawaiId = (int) $pegawai->id;
            $npp = $pegawai->npp ?? $pegawaiId;

            if (!$request->hasFile('foto') || !$request->file('foto')->isValid()) {
                return redirect()->route('pegawai.biodata.edit')->with('error', 'File foto tidak valid.');
            }

            $existingPhoto = DB::table('pegawai_biodata')
                ->where('id_pegawai', $pegawaiId)
                ->value('foto');

            $fotoName = 'pegawai_' . preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $npp) . '_' . time() . '.jpg';
            $request->file('foto')->move(public_path('assets/foto_pegawai'), $fotoName);

            DB::table('pegawai_biodata')->updateOrInsert(
                ['id_pegawai' => $pegawaiId],
                ['foto' => $fotoName]
            );

            if (!empty($existingPhoto) && $existingPhoto !== $fotoName) {
                $oldPath = public_path('assets/foto_pegawai/' . $existingPhoto);
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            return redirect()->route('pegawai.biodata.edit')->with('status', 'Foto profil berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('pegawai.biodata.edit')->with('error', 'Gagal upload foto: ' . $e->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        $storedHash = (string) ($pegawai->paswd ?? '');
        $inputCurrent = (string) $request->input('current_password');

        $isLaravelHashValid = !empty($storedHash) && Hash::check($inputCurrent, $storedHash);
        $isLegacyMd5Valid = strlen($storedHash) === 32 && ctype_xdigit($storedHash) && hash_equals(strtolower($storedHash), md5($inputCurrent));

        if (!$isLaravelHashValid && !$isLegacyMd5Valid) {
            return redirect()->route('pegawai.biodata.edit')->with('error', 'Password saat ini tidak sesuai.');
        }

        DB::table('pegawai')
            ->where('id', (int) $pegawai->id)
            ->update([
                'paswd' => Hash::make((string) $request->input('password')),
            ]);

        return redirect()->route('pegawai.biodata.edit')->with('status', 'Password berhasil diperbarui.');
    }

    public function downloadCv()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $cv = $this->getCvData((int) $pegawai->id);

        if (!$cv) {
            return redirect()->route('pegawai.biodata.edit')->with('error', 'Data CV tidak ditemukan.');
        }

        $html = view('pegawai.biodata.cv_pdf', ['cv' => $cv])->render();

        $mpdf = new Mpdf([
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 12,
            'margin_bottom' => 12,
        ]);

        $mpdf->WriteHTML($html);
        $filename = 'CV_' . ($cv->npp ?: $cv->id_pegawai) . '.pdf';

        return response($mpdf->Output($filename, 'D'))
            ->header('Content-Type', 'application/pdf');
    }

    public function downloadCvExcel()
    {
        $pegawai = Auth::guard('pegawai')->user();

        if (!$pegawai) {
            abort(403);
        }

        $cv = $this->getCvData((int) $pegawai->id);

        if (!$cv) {
            return redirect()->route('pegawai.biodata.edit')->with('error', 'Data CV tidak ditemukan.');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('CV Pegawai');

        $rows = [
            ['Field', 'Nilai'],
            ['NPP', $cv->npp],
            ['Nama Lengkap', $cv->nama_lengkap],
            ['NIDN', $cv->nidn],
            ['Jenis Kelamin', $cv->jenis_kelamin],
            ['Tempat Lahir', $cv->tempat_lahir],
            ['Tanggal Lahir', $cv->tanggal_lahir],
            ['Agama', $cv->agama],
            ['Status Nikah', $cv->status_nikah],
            ['Alamat', $cv->alamat],
            ['Provinsi', $cv->provinsi],
            ['Kota/Kabupaten', $cv->kotakab],
            ['Kecamatan', $cv->kecamatan],
            ['Kelurahan', $cv->kelurahan],
            ['Email', $cv->email1],
            ['No. HP', $cv->nohp],
            ['No. Telp', $cv->notelp],
            ['No. KTP', $cv->no_ktp],
            ['No. KK', $cv->no_kk],
            ['Gelar Depan', $cv->gelar_depan],
            ['Gelar Belakang', $cv->gelar_belakang],
            ['Status Pegawai', $cv->status_pegawai],
            ['Homebase', $cv->homebase],
        ];

        foreach ($rows as $index => $row) {
            $sheet->setCellValue('A' . ($index + 1), $row[0]);
            $sheet->setCellValue('B' . ($index + 1), (string) ($row[1] ?? ''));
        }

        $sheet->getColumnDimension('A')->setWidth(28);
        $sheet->getColumnDimension('B')->setWidth(60);

        $filename = 'CV_' . ($cv->npp ?: $cv->id_pegawai) . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function getCvData(int $pegawaiId): ?object
    {
        return DB::table('pegawai as p')
            ->leftJoin('pegawai_biodata as b', 'b.id_pegawai', '=', 'p.id')
            ->where('p.id', $pegawaiId)
            ->select(
                'p.id as id_pegawai',
                'p.npp',
                'b.nama_lengkap',
                'b.nidn',
                'b.jenis_kelamin',
                'b.tempat_lahir',
                'b.tanggal_lahir',
                'b.agama',
                'b.status_nikah',
                'b.alamat',
                'b.provinsi',
                'b.kotakab',
                'b.kecamatan',
                'b.kelurahan',
                'b.email1',
                'b.nohp',
                'b.notelp',
                'b.no_ktp',
                'b.no_kk',
                'b.gelar_depan',
                'b.gelar_belakang',
                'b.status_pegawai',
                'b.homebase',
                'b.foto'
            )
            ->first();
    }

    private function buildEditPayload(object $query): array
    {
        $data = [];
        $data['query'] = $query;
        $data['CurrentPage'] = 'content';
        $data['master_prodi'] = DB::table('master_program_studi')->get();
        $data['universitas'] = DB::table('master_universitas')->orderBy('id', 'asc')->get();
        $data['jenis_kelamin'] = ['L' => 'Laki-laki', 'P' => 'Perempuan'];
        $data['status_kawin'] = ['Lajang', 'Kawin'];
        $data['agama'] = ['Islam', 'Kristen', 'Katholik', 'Budha', 'Hindhu'];
        $data['status'] = ['aktif', 'cuti', 'keluar', 'meninggal'];
        $data['wilayah'] = DB::table('wilayah')->where('id_induk_wilayah', '000000')->get();
        $data['list_kota'] = [];
        $data['list_kecamatan'] = [];

        if (!empty($query->provinsi)) {
            $data['list_kota'] = DB::table('wilayah')->where('id_induk_wilayah', $query->provinsi)->get();
        }

        if (!empty($query->kotakab)) {
            $data['list_kecamatan'] = DB::table('wilayah')->where('id_induk_wilayah', $query->kotakab)->get();
        }

        $data['s1'] = DB::table('pegawai_riwayat_pendidikan')->where('id_pegawai', $query->pegawai_id)->where('jenjang', 'S1')->first();
        $data['s2'] = DB::table('pegawai_riwayat_pendidikan')->where('id_pegawai', $query->pegawai_id)->where('jenjang', 'S2')->first();
        $data['s3'] = DB::table('pegawai_riwayat_pendidikan')->where('id_pegawai', $query->pegawai_id)->where('jenjang', 'S3')->first();
        $data['homebase'] = DB::table('pegawai_homebase')->get();

        return $data;
    }

    private function parseDate(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $timestamp = strtotime($value);

        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }
}
