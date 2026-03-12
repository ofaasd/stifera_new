<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Negara;
use App\Models\ProgramStudi;
use App\Models\PmbGelombang;
use App\Models\Wilayah;
use App\Models\ProvKotaNew;
use App\Models\PmbPesertum as PmbPeserta;
use Illuminate\Support\Facades\Hash;
use DB;

class PmbController extends Controller
{
    //
    public function index(){
        $query['title'] = "Daftar Calon Mahasiswa Baru";
        $query['CurrentPage'] = 'content';
        $query['year_now'] = date('Y');  
        $query['status'] = [1=>'Aktif', 'Cuti', 'keluar', 'Lulus', 'Meninggal', 'DO'];
        $query['no'] = 1;
        return view('admin.pmb.index', $query);
    }
    public function create(){
        $query['title'] = "Tambah Calon Mahasiswa Baru";
        $query['CurrentPage'] = 'content';
        $query['warga_negara'] = Negara::all(); 
        $query['prodi']        = ProgramStudi::all();
        $query['kelas'] = DB::table('pmb_kelas')
            ->where('is_active', 1)
            ->get();
        $query['gelombang'] = PmbGelombang::all(); 
        $query['wilayah'] = DB::table('wilayah')
            ->where('id_induk_wilayah', '000000')
            ->get(); 
        $query['gel_ta'] = DB::table('pmb_ta')->get();
        return view('admin.pmb.create', $query);
    }
    public function store(Request $request){
        // 1. Ambil data dasar
        $gel_ta = $request->input('gel_ta');
        $jalur = $request->input('jalur');
        $gelombang_id = $request->input('gelombang');
        
        // 2. Ambil informasi gelombang jika ada
        $qr = DB::table('pmb_gelombang')->where('id', $gelombang_id)->first();
        $gelombang = $qr ? $qr->nama_gel : null;

        // 3. GENERATE NOPEN (Nomor Pendaftaran)
        $cek_nopen = DB::table('pmb_peserta')
            ->where('angkatan', $gel_ta)
            ->orderBy('id', 'DESC')
            ->first(); // Ambil 1 baris terakhir saja

        if ($cek_nopen) {
            $set_nopen = $cek_nopen->nopen + 1;
        } else {
            $set_nopen = $qr ? $qr->no_gel : 5000;
        }

        // 4. Inisialisasi variabel tambahan berdasarkan jalur
        $pmdp = null;
        $is_kerjasama = null;
        $is_mou = null;

        if ($jalur == 1) { // PMDP
            $pmdp = ($request->input('smt1') + $request->input('smt2') + $request->input('smt3') + $request->input('smt4') + $request->input('smt5')) / 5;
            
            // Penanganan Sertifikat (Jika Jalur 1)
            $file1 = $this->uploadSertifikat($request, 'file1', $set_nopen);
            $file2 = $this->uploadSertifikat($request, 'file2', $set_nopen);
            $file3 = $this->uploadSertifikat($request, 'file3', $set_nopen);

            DB::table('piagam_pmb')->insert([
                'nopen' => $set_nopen,
                'file1' => $file1,
                'ket1'  => $request->input('ket1'),
                'file2' => $file2,
                'ket2'  => $request->input('ket2'),
                'file3' => $file3,
                'ket3'  => $request->input('ket3')
            ]);

        } elseif ($jalur == 2) { // Kerjasama
            $is_kerjasama = $request->input('kerjasama');
            $is_mou       = $request->input('nama_sekolah_mou');
        }

        // 5. Upload Foto Peserta
        $nama_foto = 'default.png';
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $file = $request->file('foto');
            $nama_foto = 'pmb_peserta_' . $set_nopen . '.' . $file->getClientOriginalExtension();
            // Simpan ke folder public/assets/foto_pmb_peserta
            $file->move(public_path('assets/foto_pmb_peserta'), $nama_foto);
        }

        // 6. Ambil Tahun Ajaran Aktif
        $ta = DB::table('master_tahun_ajaran')->where('is_aktif', 1)->first();
        
        // Parse Kelas (jalur-id)
        $kelas_input = explode('-', $request->input('kelas'));
        $kelas_id = $kelas_input[0] ?? null; // Ambil nilai pertama

        // 7. Simpan ke database
        DB::table('pmb_peserta')->insert([
            'nopen'             => $set_nopen,
            'nisn'              => $request->input('nisn'),
            'gelombang'         => $gelombang,
            'noktp'             => $request->input('ktp'),
            'nama'              => $request->input('nama'),
            'jk'                => $request->input('jk'),
            'agama'             => $request->input('agama'),
            'nama_ibu'          => $request->input('ibu'),
            'nama_ayah'         => $request->input('ayah'),
            'hp_ortu'           => $request->input('hp_ortu'),
            'alamat_semarang'   => $request->input('alamat_semarang'),
            'tinggi_badan'      => $request->input('tb'),
            'berat_badan'       => $request->input('bb'),
            'tempat_lahir'      => $request->input('tl'),
            'tanggal_lahir'     => $request->input('tgl'),
            'alamat'            => $request->input('alamat'),
            'rt'                => $request->input('rt'),
            'rw'                => $request->input('rw'),
            'kelurahan'         => $request->input('kelurahan'),
            'kecamatan'         => $request->input('kecamatan'),
            'kotakab'           => $request->input('kotakab'),
            'provinsi'          => $request->input('provinsi'),
            'kodepos'           => $request->input('pos'),
            'telpon'            => $request->input('telepon'),
            'hp'                => $request->input('hp'),
            'email'             => $request->input('email'),
            'foto_peserta'      => $nama_foto,
            'asal_sekolah'      => $request->input('asal_sekolah'),
            'warga_negara'      => $request->input('warga_negara'),
            'peringkat_pmdp'    => $pmdp,
            'jalur_pendaftaran' => $jalur,
            'is_kerjasama'      => $is_kerjasama,
            'is_mou'            => $is_mou,
            'jenis_pendaftaran' => $request->input('pendaftaran'),
            'kelas'             => $kelas_id, 
            'pilihan1'          => $request->input('pilihan1'),
            'pilihan2'          => $request->input('pilihan2'),
            'info_pmb'          => $request->input('info_pmb'),
            'ukuran_seragam'    => $request->input('ukuran_seragam'),
            'is_bayar'          => '0',
            'is_online'         => '0',
            'admin_input'       => session('user_id'),
            'angkatan'          => $gel_ta,
            'tahun_ajaran'      => $ta ? $ta->id : null,
            'is_delete'         => '0',
            'is_mundur'         => '0',
            'admin_input_date'  => now() // Helper waktu Laravel (menggantikan date('Y-m-d H:i:s'))
        ]);

        // Redirect sesuai kebutuhan
        return redirect('pmb')->with('success', 'Calon Mahasiswa Berhasil Ditambahkan');
    }
    public function edit(String $id){
        $query['title'] = "Edit Calon Mahasiswa Baru";
        $query['CurrentPage'] = 'content';
        $query['warga_negara'] = Negara::all(); 
        $query['prodi']        = ProgramStudi::all();
        $query['kelas'] = DB::table('pmb_kelas')
            ->where('is_active', 1)
            ->get();
        $query['gelombang'] = PmbGelombang::all(); 
        $query['wilayah'] = DB::table('wilayah')
            ->where('id_induk_wilayah', '000000')
            ->get(); 
        $query['gel_ta'] = DB::table('pmb_ta')->get();
        $query['data'] = PmbPeserta::find($id);
        $query['list_kota'] = [];
        $query['list_kecamatan'] = [];
        if(!empty($query['data']->provinsi)){
            $query['nm_kab'] = \DB::table('wilayah')->where('id_wil', $query['data']->kotakab)->first()->nm_wil;
            $query['list_kota'] = \DB::table('wilayah')->where('id_induk_wilayah', $query['data']->provinsi)->get();
        }
        $query['nm_sekolah'] = '';
        if(!empty($query['data']->asal_sekolah)){
            $query['nm_sekolah'] = \DB::table('pmb_schools')->where('id',$query['data']->asal_sekolah)->first()->nama;
        }
        if(!empty($query['data']->kotakab)){
            $query['nm_kec'] = \DB::table('wilayah')->where('id_wil', $query['data']->kecamatan)->first()->nm_wil;
            $query['list_kecamatan'] = \DB::table('wilayah')->where('id_induk_wilayah', $query['data']->kotakab)->get();
        }
        $query['d'] = PmbPeserta::find($id);
        return view('admin.pmb.edit', $query);
    }
    public function update(String $id, Request $request){
        $nopen = $request->input('nopen');
        $id = $request->input('id');

        // 2. Validasi File Upload (opsional tapi sangat direkomendasikan)
        $request->validate([
            'foto' => 'nullable|image|mimes:jpg,png,jpeg|max:1048', // 1MB Max
        ]);

        // 3. Proses Upload Foto
        $nama_foto = null; // Default null agar tidak menimpa jika tidak ada upload
        
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $file = $request->file('foto');
            
            // Buat nama file sesuai format CI: pmb_peserta_nopen.ext
            $nama_foto = 'pmb_peserta_' . $nopen . '.' . $file->getClientOriginalExtension();
            
            // Opsional: Hapus foto lama jika ada
            $oldData = DB::table('pmb_peserta')->where('id', $id)->first();
            if ($oldData && $oldData->foto_peserta && $oldData->foto_peserta != 'default.png') {
                File::delete(public_path('assets/foto_pmb_peserta/' . $oldData->foto_peserta));
            }
            
            // Pindahkan ke folder tujuan di public/assets/foto_pmb_peserta
            $file->move(public_path('assets/foto_pmb_peserta'), $nama_foto);
        }

        // 4. Siapkan array data yang akan diupdate
        $data = [
            'nisn'             => $request->input('nisn'),
            'gelombang'        => $request->input('gelombang'),
            'noktp'            => $request->input('noktp'),
            'nama'             => $request->input('nama'),
            'jk'               => $request->input('jk'),
            'agama'            => $request->input('agama'),
            'nama_ibu'         => $request->input('nama_ibu'),
            'nama_ayah'        => $request->input('nama_ayah'),
            'tinggi_badan'     => $request->input('tinggi_badan'),
            'berat_badan'      => $request->input('berat_badan'),
            'tempat_lahir'     => $request->input('tempat_lahir'),
            'tanggal_lahir'    => $request->input('tanggal_lahir'),
            'alamat'           => $request->input('alamat'),
            'rt'               => $request->input('rt'),
            'rw'               => $request->input('rw'),
            'kelurahan'        => $request->input('kelurahan'),
            'kecamatan'        => $request->input('kecamatan'),
            'kotakab'          => $request->input('kotakab'),
            'provinsi'         => $request->input('provinsi'),
            'kodepos'          => $request->input('kode_pos'),
            'telpon'           => $request->input('telpon'),
            'hp'               => $request->input('hp'),
            'email'            => $request->input('email'),
            'asal_sekolah'     => $request->input('asal_sekolah'),
            'warga_negara'     => $request->input('warga_negara'),
            'pilihan1'         => $request->input('pilihan1'),
            'pilihan2'         => $request->input('pilihan2'),
            'info_pmb'         => $request->input('info_pmb'),
            'ukuran_seragam'   => $request->input('ukuran_seragam'),
            'admin_input'      => session('user_id'),
            'admin_input_date' => now(), // Helper waktu Laravel (menggantikan date())
        ];

        // Jika ada foto baru yang diunggah, tambahkan ke array update.
        // Jika tidak ada foto baru, biarkan foto lama tetap ada di database.
        if ($nama_foto !== null) {
            $data['foto_peserta'] = $nama_foto;
        }

        // 5. Eksekusi query update
        $r = DB::table('pmb_peserta')
            ->where('id', $id)
            ->update($data);
        
        // REKOMENDASI LARAVEL:
        return redirect()->back()->with('success', 'Data calon mahasiswa berhasil diperbarui!');
    }
    private function uploadSertifikat(Request $request, $inputName, $nopen)
    {
        if ($request->hasFile($inputName) && $request->file($inputName)->isValid()) {
            $file = $request->file($inputName);
            $fileName = 'sertifikat_peserta_' . $nopen . '_' . rand() . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke public/assets/sertifikat
            $file->move(public_path('assets/sertifikat'), $fileName);
            
            return $fileName;
        }
        return null;
    }

    public function index_nim(){
        $query['title'] = "Generate NIM";
        $query['CurrentPage'] = 'content';
        $query['year_now'] = date('Y');  
        $query['status'] = [1=>'Aktif', 'Cuti', 'keluar', 'Lulus', 'Meninggal', 'DO'];
        $query['no'] = 1;
        return view('admin.pmb.index_nim', $query);
    }
    public function preview_generate_nim(String $tahun){
        // 1. Data selesai (NIM tidak boleh NULL)
        $query['title'] = "Preview Generate NIM";
        $query['CurrentPage'] = 'content';
        $data_selesai = DB::table('pmb_peserta')
            ->where('angkatan', $tahun)
            ->whereNotNull('nim') 
            // Jika di database NIM kosong disimpan sebagai string kosong (''), gunakan line di bawah ini juga:
            // ->where('nim', '!=', '') 
            ->get();

        // 2. Data belum selesai (NIM masih NULL)
        $data = DB::table('pmb_peserta')
            ->where('angkatan', $tahun)
            ->whereNull('nim')
            ->get();

        // 3. Query Kelas dan konversi ke Array Asosiatif [id => nama_kelas]
        // Fungsi pluck() otomatis menggantikan perulangan foreach yang Anda buat di CI
        $a = DB::table('pmb_kelas')
            ->where('is_active', 1)
            ->pluck('nama_kelas', 'id')
            ->toArray();

        // 4. Data Program Studi
        $jurusan = DB::table('program_studi')->get();

        // 5. Mengemas (Compact) semua variabel untuk dikirim ke view
        $viewData = [
            'data_selesai' => $data_selesai,
            'data'         => $data,
            'ta'           => $tahun,
            'a'            => $a,
            'jurusan'      => $jurusan,
            'title'        => $query['title'],
            'CurrentPage'  => $query['CurrentPage'],
        ];
        return view('admin.pmb.preview_generate_nim', $viewData);
    }
    public function req_data(Request $request){
        $data['data'] = DB::table('pmb_peserta')->where([
            'angkatan'  => $request->input('tahun'),
            'is_delete' => 0
        ])->get();
        return view('admin.pmb.req_data', $data);
    }
    public function daftar_sekolah(Request $request){
        $id_prov = $request->id_prov;
        $id_kota = $request->id_kota;
        $get_kab = Wilayah::where('id_wil',$id_kota)->first();
        $name_kab = $get_kab->nm_wil;
        $get_code_wil = $get_kab->id_induk_wilayah;    
        $get_prov = Wilayah::where('id_wil',$get_code_wil)->first();
        $name_prov = $get_prov->nm_wil;
        $get_name_prov = str_replace("Prop. ","", $name_prov);
        $get_name_kab = str_replace("Kab. ", "", $name_kab);
        $query = ProvKotaNew::where('nama_prov','like','%' .$get_name_prov.'%')->where('nama_kota','like','%' .$get_name_kab.'%')->first();
        // $query = $this->db->query('select * from prov_kota_new where (nama_prov like "%'.$get_name_prov.'%") and (nama_kota like "%'.$get_name_kab.'%")')->result_array();
        $prov_id = $query->prov_id;
        $kota_id = $query->kota_id;
        $where = array(  
                        'propinsi' => $prov_id
                        // 'daerah' => $kota_id
                        );
        $r = DB::table('pmb_schools')->where($where)->get();
        return response()->json($r);
    }
    public function daftar_mou(Request $request){
        $id = $request->id;
        $data = [];
        if ($id == 1) {
            # code...
            $data = DB::table('pmb_mou_afkar')->get();
            
        }
        return response()->json($data);
    }
    public function nopen_gel(Request $request){
        $nopen_pmdp = $request->id;
        // $nopen_pmdp = 20000;
        $query = DB::table('pmb_peserta')->where(['nopen' => $nopen_pmdp]);
        $cek = $query->count();
        if ($cek < 1) {
            # code...
            $data = array(array("nopen" => "50000"));
        }else{
            $q = DB::table('pmb_peserta')->where('gelombang','like',"GEL%")->orderBy('id','desc')->first();
            $nopen_baru = $q->nopen + 1;
            $data = array(array("nopen" => "$nopen_baru"));
        }
        return response()->json($data);
    }
    public function cek_nik(Request $request){
        $result_count = DV::table('pmb_peserta')->where(['noktp' => $request->nik])->count();
        
        echo json_encode([
            'nik_tersedia' => $result_count
        ]);
    } 
    public function saveVerifikasi(Request $request){
        $nopen          = $request->input('nopen');
        $id_bank        = $request->input('id_bank');
        $norek_pengirim = $request->input('norek_pengirim');
        $an_pengirim    = $request->input('an_pengirim');
        $tgl_tf         = $request->input('tgl_tf');

        // 2. Validasi file (opsional tapi sangat disarankan)
        $request->validate([
            'bukti' => 'nullable|image|mimes:jpg,png,jpeg|max:1024', // max dalam Kilobytes
        ]);

        // 3. Proses Upload Bukti (jika ada file yang diunggah)
        $bukti = null;
        if ($request->hasFile('bukti') && $request->file('bukti')->isValid()) {
            $file = $request->file('bukti');
            
            // Buat nama file sesuai format CI: bukti_peserta_nopen.ext
            $fileName = 'bukti_peserta_' . $nopen . '.' . $file->getClientOriginalExtension();
            
            // Pindahkan ke folder public/assets/bukti/
            $file->move(public_path('assets/bukti'), $fileName);
            
            $bukti = $fileName;
        }

        // 4. Mulai Database Transaction
        try {
            DB::beginTransaction();

            // Update status is_bayar di tabel pmb_peserta
            DB::table('pmb_peserta')
                ->where('nopen', $nopen)
                ->update(['is_bayar' => 1]);

            // Siapkan data untuk tabel bukti_registrasi
            $dataBukti = [
                'id_rekening'    => $id_bank,
                'norek_pengirim' => $norek_pengirim,
                'an_pengirim'    => $an_pengirim,
                'tgl_tf'         => $tgl_tf,
            ];

            // Jika ada gambar baru yang diunggah, masukkan ke array update.
            // Jika tidak ada, kolom 'bukti' di DB tidak akan ditimpa dengan NULL.
            if ($bukti !== null) {
                $dataBukti['bukti'] = $bukti;
            }

            // Simpan ke bukti_registrasi (Otomatis cek: Update jika ada, Insert jika belum)
            DB::table('bukti_registrasi')->updateOrInsert(
                ['nopen' => $nopen], // Kondisi pencarian (Where)
                $dataBukti           // Data yang akan disimpan
            );

            // Commit perubahan ke database
            DB::commit();

            // Redirect dengan membawa pesan sukses
            return redirect('pmb')->with('success', 'Verifikasi pembayaran berhasil disimpan!');

        } catch (\Exception $e) {
            // Batalkan jika ada error
            DB::rollBack();
            
            return redirect()->back()->with('error', 'Gagal menyimpan verifikasi: ' . $e->getMessage());
        }
    }
    public function registrasi(String $id){
        $pmb['title'] = "Registrasi PMB";
        $pmb['CurrentPage'] = 'content';

        // 1. Ambil data peserta
        $peserta = DB::table('pmb_peserta')->where('id', $id)->first();

        // Opsional: Proteksi jika ID peserta tidak ditemukan
        if (!$peserta) {
            return redirect()->back()->with('error', 'Data peserta tidak ditemukan.');
        }

        $nopen = $peserta->nopen;
        $kelas = $peserta->kelas;

        // 2. Ambil data keuangan berdasarkan kelas peserta
        $keuangan = DB::table('pmb_keuangan')->where('kelas', $kelas)->first();
        $pmb['o'] = $keuangan;
        // 3. Ambil data master dan custom model
        // Asumsi: Method di Model_pmb sudah diubah menjadi static method di Laravel (ModelPmb::)
        $pmb['registrasi']   = PmbPeserta::find($id);
        $pmb['d'] = $pmb['registrasi'];
        $pmb['warga_negara'] = Negara::all(); 
        $pmb['prodi']        = ProgramStudi::all();
        $pmb['data']         = $this->getDetailReferensiPeserta($id);

        // 4. Query Builder untuk master data
        $pmb['gelombang'] = DB::table('pmb_gelombang')->get(); // Menggantikan get()->result()
        $pmb['wilayah']   = DB::table('wilayah')->where('id_induk_wilayah', '000000')->get();

        // 5. Cek apakah sudah ada data di tabel pmb_registrasi
        $registrasi = DB::table('pmb_registrasi')->where('nopen', $nopen)->first();

        if ($registrasi) {
            // Jika sudah ada (menggantikan $c->num_rows() > 0)
            $pmb['spi']            = $registrasi->spi;
            $pmb['potongan_spi']   = $registrasi->potongan_spi;
            $pmb['jml_tahap_1']    = $registrasi->jml_tahap_1;
            $pmb['jml_tahap_2']    = $registrasi->jml_tahap_2;
            $pmb['jml_tahap_3']    = $registrasi->jml_tahap_3;
            $pmb['tgl_tahap_1']    = $registrasi->tgl_tahap_1;
            $pmb['tgl_tahap_2']    = $registrasi->tgl_tahap_2;
            $pmb['tgl_tahap_3']    = $registrasi->tgl_tahap_3;
            $pmb['status_tahap_1'] = $registrasi->status_tahap_1;
            $pmb['status_tahap_2'] = $registrasi->status_tahap_2;
            $pmb['status_tahap_3'] = $registrasi->status_tahap_3;
            $pmb['sks']            = $registrasi->sks;
            $pmb['kemahasiswaan']  = $registrasi->kemahasiswaan;
            $pmb['operasional']    = $registrasi->operasional;
            $pmb['seragam']        = $registrasi->seragam;
        } else {
            // Jika belum ada, gunakan standar default dari pmb_keuangan ($keuangan)
            // Note: Penggunaan ?-> mencegah error jika $keuangan kebetulan NULL di database
            $pmb['spi']            = $keuangan?->total_spi ?? 0;
            $pmb['potongan_spi']   = 0;
            $pmb['jml_tahap_1']    = 0;
            $pmb['jml_tahap_2']    = 0;
            $pmb['jml_tahap_3']    = 0;
            $pmb['tgl_tahap_1']    = 0;
            $pmb['tgl_tahap_2']    = 0;
            $pmb['tgl_tahap_3']    = 0;
            $pmb['status_tahap_1'] = 0;
            $pmb['status_tahap_2'] = 0;
            $pmb['status_tahap_3'] = 0;
            $pmb['sks']            = $keuangan?->total_sks ?? 0;
            $pmb['kemahasiswaan']  = $keuangan?->kemahasiswaan ?? 0;
            $pmb['operasional']    = $keuangan?->operasional ?? 0;
            $pmb['seragam']        = $keuangan?->seragam ?? 0;
        }

        // 6. Menggabungkan data dan melempar ke View
        $viewData = array_merge($pmb);
        return view('admin.pmb.registrasi', $viewData);
    }
    private function getDetailReferensiPeserta($id)
    {
        // 1. Ambil data peserta utama
        $peserta = DB::table('pmb_peserta')->where('id', $id)->first();

        // Proteksi: Jika peserta tidak ditemukan, kembalikan array kosong
        if (!$peserta) {
            return [
                'nama_negara' => '', 'nm_prop' => '', 'nm_kab' => '', 'nm_kec' => '',
                'nm_sekolah' => '', 'gelombang' => '', 'jurusan1' => '', 'jurusan2' => '', 'mou' => ''
            ];
        }

        // 2. Ambil data relasi (Gunakan first() menggantikan result_array()[0])
        $negara    = DB::table('negara')->where('id_negara', $peserta->warga_negara)->first();
        $prop      = DB::table('wilayah')->where('id_wil', $peserta->provinsi)->first();
        $kab       = DB::table('wilayah')->where('id_wil', $peserta->kotakab)->first();
        $kec       = DB::table('wilayah')->where('id_wil', $peserta->kecamatan)->first();
        $sekolah   = DB::table('pmb_schools')->where('id', $peserta->asal_sekolah)->first();
        $gelombang = DB::table('pmb_gelombang')->where('nama_gel', $peserta->gelombang)->first();
        $jurusan1  = DB::table('program_studi')->where('kode', $peserta->pilihan1)->first();
        $jurusan2  = DB::table('program_studi')->where('kode', $peserta->pilihan2)->first();
        $mou       = DB::table('pmb_mou_afkar')->where('id_sekolah', $peserta->is_mou)->first();

        // 3. Susun ke dalam array menggunakan Nullsafe operator (?->) dan fallback (?? '')
        // Jika $prop bernilai null, maka $prop?->nm_wil tidak akan error, melainkan mengembalikan string kosong ('')
        $data = [
            'nama_negara' => $negara?->nm_negara ?? '-',
            'nm_prop'     => $prop?->nm_wil ?? '-',
            'nm_kab'      => $kab?->nm_wil ?? '-',
            'nm_kec'      => $kec?->nm_wil ?? '-',
            'nm_sekolah'  => $sekolah?->nama ?? '-',
            'gelombang'   => $gelombang?->nama_gel_long ?? '-',
            'jurusan1'    => $jurusan1?->nama_jurusan ?? '-',
            'jurusan2'    => $jurusan2?->nama_jurusan ?? '-',
            'mou'         => $mou?->nama_sekolah ?? '-'
        ];

        return $data;
    }
    public function tahap_1(Request $request)
    {
        DB::table('pmb_registrasi')->updateOrInsert(
            ['nopen' => $request->input('nopen')], // Kondisi pencarian
            ['status_tahap_1' => $request->input('tahap_1')] // Data yang disimpan/diupdate
        );

        return response()->json(['res' => 1]);
    }

    public function tahap_2(Request $request)
    {
        DB::table('pmb_registrasi')->updateOrInsert(
            ['nopen' => $request->input('nopen')],
            ['status_tahap_2' => $request->input('tahap_2')]
        );

        return response()->json(['res' => 1]);
    }

    public function tahap_3(Request $request)
    {
        DB::table('pmb_registrasi')->updateOrInsert(
            ['nopen' => $request->input('nopen')],
            ['status_tahap_3' => $request->input('tahap_3')]
        );

        return response()->json(['res' => 1]);
    }

    public function tahap_4(Request $request)
    {
        // Catatan: Pada kode CI asli Anda ada typo '$this->db>get_where'
        // Di Laravel dengan updateOrInsert, masalah typo itu otomatis teratasi.
        DB::table('pmb_registrasi')->updateOrInsert(
            ['nopen' => $request->input('nopen')],
            ['status_tahap_4' => $request->input('tahap_4')]
        );

        return response()->json(['res' => 1]);
    }
    private function export_mhs(Request $request)
    {
        // Asumsi: Jika generatepass() di Authact mengembalikan MD5/Bcrypt custom,
        // panggil via Facade atau Service Container. 
        // Jika Anda ingin beralih ke Laravel Hash standar, gunakan: Hash::make('demo12345')
        $password = Hash::make('demo12345'); 
        
        $cek_bayar = $request->input('bayar');
        $nopen     = $request->input('nopen');

        // Jika belum bayar, langsung hentikan eksekusi
        if ($cek_bayar == 0) {
            return 0; // atau return false;
        }

        // 1. Ambil data peserta PMB (first() menggantikan result_array()[0])
        $generate_nim = DB::table('pmb_peserta')->where('nopen', $nopen)->first();

        // Proteksi: Jika data peserta tidak ditemukan
        if (!$generate_nim) {
            return 0;
        }

        // 2. Ambil Kode NIM dari Program Studi
        $prodi = DB::table('program_studi')->where('id', $generate_nim->pilihan1)->first();
        
        // Proteksi: Jika program studi tidak ditemukan
        if (!$prodi) {
            return 0;
        }

        $kodeNim = $prodi->kodenim;
        
        // 3. Bentuk NIM Baru dan Email
        $new_nim = $kodeNim . date('y') . $generate_nim->nopen;
        $email   = $new_nim . "@nusaputera.ac.id";

        // 4. Siapkan Data untuk dimasukkan ke tabel mahasiswa
        $dataMhs = [
            'nim'              => $new_nim,
            'nims'             => $new_nim,
            'nama'             => $request->input('nama'),
            'alamat'           => $request->input('alamat'),
            'rt'               => $request->input('rt'),
            'rw'               => $request->input('rw'),
            'kelurahan'        => $request->input('kelurahan'),
            'kecamatan'        => $request->input('kecamatan'),
            'kokab'            => $request->input('kotakab'),
            'provinsi'         => $request->input('provinsi'),
            'telp'             => $request->input('telpon'),
            'hp'               => $request->input('hp'),
            'email'            => $email,
            'paswd'            => $password,
            'status'           => 1,
            'foto_mhs'         => $generate_nim->foto_peserta,
            'hp_ortu'          => $generate_nim->hp_ortu,
            'alamat_semarang'  => $generate_nim->alamat_semarang,
            'ta'               => $generate_nim->tahun_ajaran,
            'id_program_studi' => $generate_nim->pilihan1,
            'angkatan'         => $generate_nim->angkatan,
            'kelas'            => $generate_nim->kelas,
            'agama'            => $generate_nim->agama
        ];

        // 5. Update data di pmb_peserta
        DB::table('pmb_peserta')
            ->where('nopen', $nopen)
            ->update([
                'is_bayar'              => $cek_bayar,
                'nim'                   => $new_nim,
                // 'admin_registrasi'   => session('user_id'),
                'admin_registrasi'      => 1,
                'admin_registrasi_date' => now() // Helper Laravel (menggantikan date('Y-m-d H:i:s'))
            ]);

        // 6. Simpan ke tabel Mahasiswa (UpdateOrInsert untuk menghindari duplikat)
        $r = DB::table('mahasiswa')->updateOrInsert(
            ['nim' => $new_nim], // Kondisi pencarian (jika nim sudah ada -> update)
            $dataMhs             // Data yang disimpan/diupdate
        );

        // updateOrInsert mengembalikan boolean (true jika berhasil)
        // Untuk mempertahankan logika CI Anda yang me-return 1 atau 0:
        return $r ? 1 : 0;
    }
    public function surat_pernyataan2(Request $request)
    {
        // Memanggil private function yang telah dibuat sebelumnya
        // Hapus baris ini jika fungsi export_mhs tidak digunakan atau sudah dipindah rute
        $r = $this->export_mhs($request);

        $id = $request->input('id');

        // 1. Ambil Data Peserta (mengganti get_where()->result_array()[0])
        $peserta = DB::table('pmb_peserta')->where('id', $id)->first();
        
        // Jika peserta tidak ditemukan, kembalikan dengan error
        if (!$peserta) {
            return redirect()->back()->with('error', 'Data peserta tidak ditemukan.');
        }

        $nopen = $peserta->nopen;

        // 2. Siapkan data tagihan pembayaran
        $data_tagihan = [
            'spi'           => $request->input('spi'),
            'potongan_spi'  => $request->input('potongan'),
            'sks'           => $request->input('sks'),
            'operasional'   => $request->input('operasional'),
            'kemahasiswaan' => $request->input('kemahasiswaan'),
            'seragam'       => $request->input('seragam'),
            'jml_tahap_1'   => $request->input('jml_tahap_1'),
            'jml_tahap_2'   => $request->input('jml_tahap_2'),
            'jml_tahap_3'   => $request->input('jml_tahap_3'),
            'jml_tahap_4'   => $request->input('jml_tahap_4'),
            'tgl_tahap_1'   => $request->input('tgl_tahap_1'),
            'tgl_tahap_2'   => $request->input('tgl_tahap_2'),
            'tgl_tahap_3'   => $request->input('tgl_tahap_3'),
            'tgl_tahap_4'   => $request->input('tgl_tahap_4'),
        ];

        // 3. Simpan atau Update ke tabel pmb_registrasi
        DB::table('pmb_registrasi')->updateOrInsert(
            ['nopen' => $nopen], // Kondisi pencarian
            $data_tagihan        // Data yang disimpan/diupdate
        );

        // 4. Penanganan Nilai Tes (Hanya jika jalur bukan PMDP(1) dan bukan Kerjasama(2) -> alias UMUM(3))
        // Perbaikan Logika: Pada CI Anda tertulis ($get_nopen[0]['jalur_pendaftaran'] != 2) || ($get_nopen[0]['jalur_pendaftaran'] != 1)
        // Logika CI itu sebenarnya salah (selalu true). Yang benar adalah menggunakan AND (&&).
        if ($peserta->jalur_pendaftaran != 1 && $peserta->jalur_pendaftaran != 2) {
            
            $tes_input = $request->input('tes');
            $score = ($tes_input == 0 || empty($tes_input)) ? null : $tes_input;

            // Simpan atau Update ke tabel pmb_nilai_tes
            DB::table('pmb_nilai_tes')->updateOrInsert(
                ['nopen' => $nopen],
                ['total_score' => $score]
            );
        }

        // 5. Redirect kembali ke halaman registrasi dengan pesan sukses
        return redirect('pmb/registrasi/' . $id)->with('success', 'Detail pembayaran berhasil disimpan.');
    }
    public function daftar_surat(){
        $query['title'] = "Surat Pengumuman Mahasiswa Baru";
        $query['CurrentPage'] = 'content';
        $query['daftar_surat'] = PmbPeserta::where(['is_bayar' => 1, 'is_delete' => 0, 'is_mundur' =>0])->get();  
        $query['no'] = 1;
        return view('admin.pmb.daftar_surat', $query);
    }
    public function surat_pengumuman(Request $request)
    {
        $id = $request->input('id');
        $pmb['statusCalon'] = $request->input('status');

        // 1. Ambil data utama peserta
        $peserta = DB::table('pmb_peserta')->where('id', $id)->first();
        
        if (!$peserta) {
            return redirect()->back()->with('error', 'Data peserta tidak ditemukan.');
        }

        $nopen = $peserta->nopen;
        $kelas = $peserta->kelas;

        // 2. Ambil data relasi lainnya
        // get() di Laravel setara dengan result() di CI (mengembalikan collection/array of objects)
        // first() di Laravel setara dengan row() di CI (mengembalikan 1 object)
        
        $pmb['cetak']         = DB::table('pmb_peserta')->where('id', $id)->get();
        $pmb['get_sks']       = DB::table('pmb_keuangan')->where('kelas', $kelas)->get();
        $qs                   = DB::table('pmb_keuangan')->where('kelas', $kelas)->first();
        $pmb['get_keuangan']  = DB::table('pmb_pernyataan')->where('nopen', $nopen)->get();
        $pmb['get_nilai']     = DB::table('pmb_nilai_tes')->where('nopen', $nopen)->take(1)->get();
        
        $strukturPegawai      = DB::table('struktur_pegawai')->where('id', 1)->first();
        $pmb['ketuaPMB']      = $strukturPegawai ? $strukturPegawai->ketua_pmb : '-';
        
        $pmb['get_gelombang'] = DB::table('pmb_gelombang')->where('nama_gel', $peserta->gelombang)->get();

        // 3. Logika Tagihan (Registrasi vs Keuangan Default)
        $registrasi = DB::table('pmb_registrasi')->where('nopen', $nopen)->first();

        if ($registrasi) {
            $pmb['spi']           = $registrasi->spi;
            $pmb['potongan_spi']  = $registrasi->potongan_spi ?? 0; // Tambahan fallback jika null
            $pmb['tgl_tahap_1']   = $registrasi->tgl_tahap_1;
            $pmb['sks']           = $registrasi->sks;
            $pmb['operasional']   = $registrasi->operasional;
            $pmb['kemahasiswaan'] = $registrasi->kemahasiswaan;
            $pmb['seragam']       = $registrasi->seragam;
        } else {
            // Jika belum ada di pmb_registrasi, ambil dari master pmb_keuangan ($qs)
            // Gunakan now() dari Laravel Carbon untuk menambah 30 hari
            $tgl_tahap_1 = now()->addDays(30)->format('Y-m-d');
            
            $pmb['spi']           = $qs ? $qs->total_spi : 0;
            $pmb['potongan_spi']  = 0;
            $pmb['tgl_tahap_1']   = $tgl_tahap_1;
            $pmb['sks']           = $qs ? $qs->total_sks : 0;
            $pmb['operasional']   = $qs ? $qs->operasional : 0;
            $pmb['kemahasiswaan'] = $qs ? $qs->kemahasiswaan : 0;
            $pmb['seragam']       = $qs ? $qs->seragam : 0;
        }

        // 4. Render HTML dari file Blade ke dalam variabel string
        // Pastikan file view Anda ada di: resources/views/pmb/surat_pengumuman.blade.php
        $html = view('admin.pmb.surat_pengumuman', $pmb)->render();

        // 5. Inisialisasi mPDF dan proses cetak
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 
            'format' => 'A4-P', 
            'default_font' => 'ayar'
        ]);

        $pdfFilePath = "Surat Pengumuman - " . $nopen . ".pdf"; 
        
        $mpdf->WriteHTML($html);
        
        // Output 'D' berarti langsung di-Download oleh browser
        // Return response() direkomendasikan di Laravel agar tidak memutus siklus aplikasi secara paksa
        return response($mpdf->Output($pdfFilePath, "D"))
                ->header('Content-Type', 'application/pdf');
    }
    public function statistik(){
        $query['title'] = "Statistik Mahasiswa Baru";
        $query['CurrentPage'] = 'content';
        $query['daftar_surat'] = PmbPeserta::where(['is_delete' => '0','is_mundur' => '0'])->get();  
        $query['no'] = 1;
        return view('admin.pmb.statistik', $query);
    }
    public function pmdp(){
        $query['title'] = "Peringkat PMDP";
        $query['CurrentPage'] = 'content';
        $query['list'] = DB::table('pmb_peserta')->where('jalur_pendaftaran', 1)->get();
        $query['prodi'] = DB::table('program_studi')->get(); // Wajib ditambahkan
        $query['no'] = 1;
        return view('admin.pmb.pmdp', $query);
    }
    public function uspi(){    
        $query['title'] = "Data USPI";
        $query['CurrentPage'] = 'content';
        $query['data'] = DB::table('pmb_keuangan')->get();
        $query['no'] = 1;
        return view('admin.pmb.uspi', $query);
    }
    public function uspi_edit(String $id){
        $query['title'] = "Data USPI";
        $query['CurrentPage'] = 'content';
        $query['a'] = DB::table('pmb_keuangan')->where('id',$id)->first();
        $query['no'] = 1;
        return view('admin.pmb.uspi_edit', $query);
    }
    public function uspi_update(Request $request)
    {
        // 1. Ambil ID yang akan di-update
        $id = $request->input('id');

        // 2. Ambil data inputan form secara spesifik menggunakan helper only()
        $data = $request->only([
            'kelas',
            'total_spi',
            'total_sks',
            'operasional',
            'kemahasiswaan',
            'seragam'
        ]);

        try {
            // 3. Eksekusi query update menggunakan Query Builder
            DB::table('pmb_keuangan')
                ->where('id', $id)
                ->update($data);

            // 4. Redirect kembali ke halaman uspi dengan membawa flash message sukses
            return redirect('pmb/uspi')->with('success', 'Data biaya keuangan berhasil diperbarui!');

        } catch (\Exception $e) {
            // Tangkap error jika terjadi kegagalan sistem/database
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }
    public function daftarSekolah(){
        $query['title'] = "Daftar Sekolah";
        $query['CurrentPage'] = 'content';
        $query['data'] = DB::table('pmb_schools')->limit(100)->get();
        $query['no'] = 1;
        return view('admin.pmb.daftar_sekolah', $query);
    }
}
