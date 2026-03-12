<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\PegawaiBiodatum;
use App\Models\JabatanFungsional;
use App\Models\MasterJadwalTemp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title = 'Pegawai';
        $CurrentPage = 'content';
        $pegawai = PegawaiBiodatum::query()
            ->select('p.*', 'b.*', 'j.nama as nama_jenis', 'p.id as pid', 
            \DB::raw('(SELECT jabatan_fungsional_sekarang FROM pegawai_jabatan_fungsional WHERE id_pegawai = p.id ORDER BY id DESC LIMIT 1) as jabatan_fungsional_sekarang'))
            ->from('pegawai as p')
            ->leftJoin('pegawai_biodata as b', 'b.id_pegawai', '=', 'p.id')
            ->leftJoin('pegawai_posisi as ps', 'ps.kode', '=', 'b.kd_posisi_pegawai')
            ->leftJoin('pegawai_jenis as j', 'j.id', '=', 'ps.id_jenis_pegawai')
            ->where('p.status', '=',  1) 
            ->orderBy('p.nama', 'asc')
            ->groupBy('p.id')
            ->get();
        $jabfung = JabatanFungsional::all();
        $list_jabfung = [];
        foreach($jabfung as $j){
            $list_jabfung[$j->id] = $j->jabatan;
        }   
        return view('admin.pegawai.index', compact('CurrentPage', 'pegawai', 'title','list_jabfung'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $query['title'] = 'Pegawai';
        $query['CurrentPage'] = 'content';
        $query['jenis_pegawai'] = \DB::table('pegawai_jenis')->get();
        $query['progdi'] = \DB::table('program_studi')->get();
        $query['fakultas'] = \DB::table('fakultas')->get();
        $query['master_prodi'] = \DB::table('master_program_studi')->get();
        $query['universitas'] = \DB::table('master_universitas')->orderBy('id', 'asc')->get();
        $query['last_id'] = \DB::table('pegawai')->latest('id')->value('id');
        $query['jenis_kelamin'] = array("L"=>"Laki-laki","P"=>"Perempuan");
        $query['golongan_darah'] = array("A","B","AB","O");
        $query['jabatan_fungsional'] = array("Asisten Ahli","Lektor","Lektor kepala","Guru Besar");
        $query['bagian'] = \DB::table('jabatan_struktural')->groupBy('bagian')->pluck('bagian');
        $query['status_kawin'] = array("Lajang","Kawin");
        $query['agama'] = array("Islam","Kristen","Katholik","Budha","Hindhu");
        $query['status'] = array('aktif','cuti','keluar','meninggal');
        $query['wilayah'] = \DB::table('wilayah')->where('id_induk_wilayah', '000000')->get();
        $query['url_simple_insert'] = url('pegawai/insert1');
        $query['url_insert_all'] = url('pegawai/save');
        $query['redirect1'] = url('pegawai/edit/');
        
        return view('admin.pegawai.create', $query);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function insert1(Request $request)
    {
        // Laravel otomatis menangani tipe request melalui Route (Route::post), 
        // jadi tidak perlu if($this->input->post()) lagi.
        
        $npp = $request->input('npp');

        // Mengecek apakah NPP sudah ada (menggantikan query manual CI)
        $nppExists = DB::table('pegawai')->where('npp', $npp)->exists();

        if ($nppExists) {
            // Di Laravel, idealnya menggunakan redirect()->back()->withErrors(...)
            // Tapi kita samakan dulu dengan output echo bawaan Anda
            return "npp sudah terdaftar";
        }

        // Menentukan password: jika kosong, gunakan NPP
        $password = $request->filled('password') ? $request->input('password') : $npp;

        // Mulai Database Transaction dengan try-catch
        try {
            DB::beginTransaction();

            // Insert ke tabel pegawai dan langsung ambil ID-nya (menggantikan insert_id)
            $id = DB::table('pegawai')->insertGetId([
                'npp'   => $npp,
                'nama'  => $request->input('nama_lengkap'),
                'usrnm' => $npp,
                'paswd' => Hash::make($password), // Menggunakan Bcrypt bawaan Laravel
            ]);

            // Insert ke tabel pegawai_biodata
            DB::table('pegawai_biodata')->insert([
                'id_pegawai'        => $id,
                'kd_posisi_pegawai' => $request->input('posisi_pegawai'),
                'id_progdi'         => $request->input('id_progdi'),
                'nama_lengkap'      => $request->input('nama_lengkap'),
                'jenis_kelamin'     => $request->input('jenis_kelamin'),
            ]);

            // Insert ke tabel relasi lainnya
            DB::table('pegawai_riwayat_pendidikan')->insert(['id_pegawai' => $id]);
            DB::table('pegawai_golongan')->insert(['id_pegawai' => $id]);
            DB::table('pegawai_jabatan_fungsional')->insert(['id_pegawai' => $id]);
            DB::table('pegawai_jabatan_struktural')->insert(['id_pegawai' => $id]);

            // Jika semua berhasil, simpan permanen ke database
            DB::commit();

            return "berhasil";

        } catch (\Exception $e) {
            // Jika ada query yang gagal, batalkan semua insert sebelumnya
            DB::rollBack();
            
            // Return pesan error untuk membantu debugging
            return "Terjadi kesalahan: " . $e->getMessage(); 
        }
    }
    public function store(Request $request)
    {
        // 1. Penanganan Upload File (Foto)
        $nama_foto = "";
        $nppInput = $request->input('npp');

        // Validasi file opsional (Hanya JPG, max 1MB)
        $request->validate([
            'foto' => 'nullable|mimes:jpg,jpeg|max:1048'
        ]);

        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            // Nama file menyesuaikan format CI sebelumnya
            $nama_foto = 'pegawai_' . $nppInput . '.jpg';
            
            // Pindahkan file ke folder public/assets/foto_pegawai/
            $request->file('foto')->move(public_path('assets/foto_pegawai'), $nama_foto);
        }

        // 2. Penyiapan Password
        // Catatan: Jika Anda memiliki fungsi authact->generatepass khusus, Anda bisa memanggilnya di sini.
        // Di bawah ini saya menggunakan standar bawaan Laravel.
        $rawPassword = $request->filled('password') ? $request->input('password') : $nppInput;
        $paswd = Hash::make($rawPassword);

        // 3. Format NPP
        $npp = $request->input('npp_depan') . $nppInput;

        // 4. Mulai Database Transaction
        try {
            DB::beginTransaction();

            // -- INSERT PEGAWAI --
            $id = DB::table('pegawai')->insertGetId([
                'npp'   => $npp,
                'nama'  => $request->input('nama_lengkap'),
                'usrnm' => $npp,
                'paswd' => $paswd,
            ]);

            // -- INSERT BIODATA --
            DB::table('pegawai_biodata')->insert([
                'id_pegawai'              => $id,
                'kd_posisi_pegawai'       => $request->input('posisi_pegawai'),
                'nama_lengkap'            => $request->input('nama_lengkap'),
                'alamat'                  => $request->input('alamat'),
                'jenis_kelamin'           => $request->input('jenis_kelamin'),
                'nidn'                    => $request->input('nidn'),
                'status_nikah'            => $request->input('status_nikah'),
                'status_pegawai'          => $request->input('posisi_pegawai'),
                'gelar_depan'             => $request->input('gelar_depan'),
                'gelar_belakang'          => $request->input('gelar_belakang'),
                'no_ktp'                  => $request->input('no_ktp'),
                'no_kk'                   => $request->input('no_kk'),
                'provinsi'                => $request->input('provinsi'),
                'kotakab'                 => $request->input('kotakab'),
                'kecamatan'               => $request->input('kecamatan'),
                'kelurahan'               => $request->input('kelurahan'),
                'golongan_darah'          => $request->input('golongan_darah'),
                'nama_pasangan'           => $request->input('nama_pasangan'),
                // Gunakan conditional agar tidak error jika tanggal kosong
                'tgl_lahir_pasangan'      => $request->filled('tgl_lahir_pasangan') ? date('Y-m-d', strtotime($request->input('tgl_lahir_pasangan'))) : null,
                'pekerjaan_pasangan'      => $request->input('pekerjaan_pasangan'),
                'jumlah_anak'             => $request->input('jumlah_anak'),
                'id_progdi'               => $request->input('homebase'),
                'homebase'                => $request->input('homebase'),
                'no_bpjs_kesehatan'       => $request->input('no_bpjs_kesehatan'),
                'no_bpjs_ketenagakerjaan' => $request->input('no_bpjs_ketenagakerjaan'),
                // 'foto'                 => $nama_foto, // Di-uncomment jika field ini ada di tabel
            ]);

            // -- INSERT RIWAYAT PENDIDIKAN (Looping Array) --
            $universitas = $request->input('universitasid', []);
            $jurusan     = $request->input('jurusanid', []);
            $jenjang     = $request->input('jenjang', []);

            $dataRiwayat = [];
            if($universitas == 999999){ // Jika pilih "Lainnya", gunakan input manual
                $nama_universitas = $request->input('universitas');
                //input ke master universitas jika belum ada
                if(!DB::table('master_universitas')->where('nama', $nama_universitas)->exists()){
                    DB::table('master_universitas')->insert(['nama' => $nama_universitas]);
                }
                $universitas = DB::last_insert_id(); // Ambil ID universitas yang baru saja diinput untuk digunakan di riwayat pendidikan
            } 
            if($jurusan == 999999){ // Jika pilih "Lainnya", gunakan input manual
                $nama_jurusan = $request->input('jurusan');
                //input ke master jurusan jika belum ada
                if(!DB::table('master_jurusan')->where('nama', $nama_jurusan)->exists()){
                    DB::table('master_jurusan')->insert(['nama' => $nama_jurusan]);
                }
                $jurusan = DB::last_insert_id(); // Ambil ID jurusan yang baru saja diinput untuk digunakan di riwayat pendidikan
            }
            foreach ($universitas as $key => $value) {
                if (!empty($value)) {
                    $dataRiwayat[] = [
                        'id_pegawai'  => $id,
                        'jenjang'     => $jenjang[$key] ?? null,
                        'universitas' => $value,
                        'jurusan'     => $jurusan[$key] ?? null
                    ];
                }
            }
            // Bulk Insert (Insert semua array sekaligus, lebih cepat daripada insert satu-satu di dalam loop)
            if (!empty($dataRiwayat)) {
                DB::table('pegawai_riwayat_pendidikan')->insert($dataRiwayat);
            }

            // -- INSERT GOLONGAN --
            if(!empty($request->golongan_skrg) && !empty($request->np_skrg) && !empty($request->nosk_skrg) && !empty($request->tmt_skrg)){
                DB::table('pegawai_golongan')->insert([
                    'id_pegawai'   => $id,
                    'nama'         => $request->input('golongan_skrg'),
                    'no_pengantar' => $request->input('np_skrg'),
                    'no_sk'        => $request->input('nosk_skrg'),
                    'tanggal_sk'   => $request->filled('tglsk_skrg') ? date('Y-m-d', strtotime($request->input('tglsk_skrg'))) : null,
                    'tmt'          => $request->input('tmt_skrg'),
                    'status'       => 'sekarang',
                ]);
            }
            // -- INSERT JABATAN FUNGSIONAL --
            if(!empty($request->jabatan_fungsional) && !empty($request->nosk_fungsional) && !empty($request->tmtsk_fungsional) && !empty($request->kum_fungsional)){
                DB::table('pegawai_jabatan_fungsional')->insert([
                    'id_pegawai'                  => $id,
                    'jabatan_fungsional_sekarang' => $request->input('jabatan_fungsional'),
                    'no_sk_fungsional'            => $request->input('nosk_fungsional'),
                    'tgl_sk_fungsional'           => $request->filled('tglsk_fungsional') ? date('Y-m-d', strtotime($request->input('tglsk_fungsional'))) : null,
                    'tmt_sk_fungsional'           => $request->input('tmtsk_fungsional'),
                    'kum'                         => $request->input('kum_fungsional'),
                ]);
            }

            // -- INSERT JABATAN STRUKTURAL --
            if(!empty($request->unit_kerja) && !empty($request->jabatan_struktural) && !empty($request->nosk_struktural) && !empty($request->tmtsk_struktural)) {
                DB::table('pegawai_jabatan_struktural')->insert([
                    'id_pegawai'            => $id,
                    'unit_kerja'            => $request->input('unit_kerja'),
                    'id_jabatan_struktural' => $request->input('jabatan_struktural'),
                    'no_sk_struktural'      => $request->input('nosk_struktural'),
                    'tanggal_sk_struktural' => $request->filled('tglsk_struktural') ? date('Y-m-d', strtotime($request->input('tglsk_struktural'))) : null,
                    'tmt_sk_struktural'     => $request->input('tmtsk_struktural'),
                ]);
            }
            // Jika semua query berhasil
            DB::commit();
            return "berhasil";

        } catch (\Exception $e) {
            // Jika gagal, rollback database dan hapus foto yang terlanjur terupload (jika ada)
            DB::rollBack();

            if ($nama_foto != "" && File::exists(public_path('assets/foto_pegawai/' . $nama_foto))) {
                File::delete(public_path('assets/foto_pegawai/' . $nama_foto));
            }

            return "gagal: " . $e->getMessage();
        }
    }
    public function reset_password(String $npp){
        $defaultPassword = $npp; // Atau bisa diganti dengan password default lain sesuai kebutuhan
        $paswd = Hash::make($defaultPassword);
        DB::table('pegawai')->where('npp', $npp)->update(['paswd' => $paswd]);
        return redirect()->back()->with('success', 'Password berhasil direset ke default (NPP).');
    }
    public function delete_pegawai(String $npp){
        DB::table('pegawai')->where('npp', $npp)->update(['status' => 0]);
        return redirect()->back()->with('success', 'Data pegawai berhasil dihapus (status diubah menjadi 0).');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $query['query'] = DB::query()
            ->select('b.*','p.npp','p.id as pegawai_id','b.id as id_biodata', 'p.nama as nama_pegawai', 
            \DB::raw('(SELECT jabatan_fungsional_sekarang FROM pegawai_jabatan_fungsional WHERE id_pegawai = p.id ORDER BY id DESC LIMIT 1) as jabatan_fungsional_sekarang'))
            ->from('pegawai as p')
            ->leftJoin('pegawai_biodata as b', 'b.id_pegawai', '=', 'p.id')
            ->leftJoin('pegawai_posisi as ps', 'ps.kode', '=', 'b.kd_posisi_pegawai')
            ->leftJoin('pegawai_jenis as j', 'j.id', '=', 'ps.id_jenis_pegawai')
            ->where('p.status', '=',  1) 
            ->where('p.id', '=', $id)
            ->orderBy('p.nama', 'asc')
            ->groupBy('p.id')
            ->first();  
        $query['title'] = 'Edit Pegawai';
        $query['CurrentPage'] = 'content';
        $query['jenis_pegawai'] = \DB::table('pegawai_jenis')->get();
        $query['progdi'] = \DB::table('program_studi')->get();
        $query['fakultas'] = \DB::table('fakultas')->get();
        $query['master_prodi'] = \DB::table('master_program_studi')->get();
        $query['universitas'] = \DB::table('master_universitas')->orderBy('id', 'asc')->get();
        $query['last_id'] = \DB::table('pegawai')->latest('id')->value('id');
        $query['jenis_kelamin'] = array("L"=>"Laki-laki","P"=>"Perempuan");
        $query['golongan_darah'] = array("A","B","AB","O");
        $query['jabatan_fungsional'] = array("Asisten Ahli","Lektor","Lektor kepala","Guru Besar");
        $query['bagian'] = \DB::table('jabatan_struktural')->groupBy('bagian')->pluck('bagian');
        $query['status_kawin'] = array("Lajang","Kawin");
        $query['agama'] = array("Islam","Kristen","Katholik","Budha","Hindhu");
        $query['status'] = array('aktif','cuti','keluar','meninggal');
        $query['wilayah'] = \DB::table('wilayah')->where('id_induk_wilayah', '000000')->get();
        $query['list_kota'] = [];
        $query['list_kecamatan'] = [];
        if(!empty($query['query']->provinsi)){
            $query['list_kota'] = \DB::table('wilayah')->where('id_induk_wilayah', $query['query']->provinsi)->get();
        }
        
        if(!empty($query['query']->kotakab)){
            $query['list_kecamatan'] = \DB::table('wilayah')->where('id_induk_wilayah', $query['query']->kotakab)->get();
        }
        $query['s1'] = \DB::table('pegawai_riwayat_pendidikan')->where('id_pegawai', $query['query']->pegawai_id)->where('jenjang', 'S1')->first();
        $query['s2'] = \DB::table('pegawai_riwayat_pendidikan')->where('id_pegawai', $query['query']->pegawai_id)->where('jenjang', 'S2')->first();
        $query['s3'] = \DB::table('pegawai_riwayat_pendidikan')->where('id_pegawai', $query['query']->pegawai_id)->where('jenjang', 'S3')->first();
        $query['url_simple_insert'] = url('pegawai/insert1');
        $query['url_insert_all'] = url('pegawai/save');
        $query['redirect1'] = url('pegawai');
        $query['homebase'] = \DB::table('pegawai_homebase')->get();
        
        return view('admin.pegawai.edit', $query);  
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        // Mengambil parameter dari form
        // Catatan: Jika Anda menggunakan Resource Route, variabel $id di parameter atas 
        // sebenarnya sudah berisi pegawai_id, tapi di sini kita ikuti alur form Anda.
        $pegawai_id = $request->input('pegawai_id'); 
        $npp = $request->input('nip');
        $id_biodata = $request->input('id_biodata');

        try {
            // Memulai Database Transaction
            DB::beginTransaction();

            // 1. UPDATE TABEL PEGAWAI
            DB::table('pegawai')
                ->where('id', $pegawai_id)
                ->update([
                    'npp' => $npp,
                ]);

            // 2. UPDATE TABEL PEGAWAI_BIODATA
            DB::table('pegawai_biodata')
                ->where('id', $id_biodata)
                ->update([
                    'kd_posisi_pegawai'       => $request->input('posisi_pegawai'),
                    'homebase'                => $request->input('homebase'),
                    'nama_lengkap'            => $request->input('nama_lengkap'),
                    'alamat'                  => $request->input('alamat'),
                    'jenis_kelamin'           => $request->input('jenis_kelamin'),
                    'nidn'                    => $request->input('nidn'),
                    'status_nikah'            => $request->input('status_nikah'),
                    'status_pegawai'          => $request->input('status'),
                    // Pengecekan agar tidak terjadi error jika input tanggal kosong
                    'tanggal_lahir'           => $request->filled('tanggal_lahir') ? date('Y-m-d', strtotime($request->input('tanggal_lahir'))) : null,
                    'tempat_lahir'            => $request->input('tempat_lahir'),
                    'gelar_depan'             => $request->input('gelar_depan'),
                    'gelar_belakang'          => $request->input('gelar_belakang'),
                    'no_ktp'                  => $request->input('no_ktp'),
                    'no_kk'                   => $request->input('no_kk'),
                    'nama_pasangan'           => $request->input('nama_pasangan'),
                    'tgl_lahir_pasangan'      => $request->filled('tgl_lahir_pasangan') ? date('Y-m-d', strtotime($request->input('tgl_lahir_pasangan'))) : null,
                    'pekerjaan_pasangan'      => $request->input('pekerjaan_pasangan'),
                    'jumlah_anak'             => $request->input('jumlah_anak'),
                    'provinsi'                => $request->input('provinsi'),
                    'kotakab'                 => $request->input('kotakab'),
                    'kecamatan'               => $request->input('kecamatan'),
                    'kelurahan'               => $request->input('kelurahan'),
                    'nohp'                    => $request->input('nohp'),
                    'notelp'                  => $request->input('notelp'),
                    'agama'                   => $request->input('agama'),
                    'email1'                  => $request->input('email1'),
                    'no_bpjs_kesehatan'       => $request->input('no_bpjs_kesehatan'),
                    'no_bpjs_ketenagakerjaan' => $request->input('no_bpjs_ketenagakerjaan'),
                ]);

            // 3. LOGIKA RIWAYAT PENDIDIKAN (Insert Master & Insert/Update Riwayat)
            $universitas    = $request->input('universitas', []);
            $universitasid  = $request->input('universitasid', []);
            $jurusan        = $request->input('jurusan', []);
            $jurusanid      = $request->input('jurusanid', []);
            $jenjang        = $request->input('jenjang', []);
            $id_pegawai     = $request->input('id_pegawai');
            $status_riwayat = $request->input('status_riwayat', []);

            foreach ($universitasid as $key => $value) {
                // Lewati jika input array ternyata kosong
                if (empty($value)) {
                    continue; 
                }

                $univ_id = $value;

                // Jika Universitas "Lainnya" (999999) dipilih, insert ke master_universitas
                if ($value == 999999) {
                    // insertGetId otomatis mengembalikan ID baru pengganti db->insert_id()
                    $univ_id = DB::table('master_universitas')->insertGetId([
                        'nama_universitas' => $universitas[$key] ?? ''
                    ]);
                }

                $prodi_id = $jurusanid[$key] ?? null;

                // Jika Jurusan "Lainnya" (999999) dipilih, insert ke master_program_studi
                if ($prodi_id == 999999) {
                    $prodi_id = DB::table('master_program_studi')->insertGetId([
                        'nama_jurusan' => $jurusan[$key] ?? ''
                    ]);
                }

                // Pengecekan apakah data riwayat sudah ada sebelumnya
                $riwayatId = $status_riwayat[$key] ?? 0;

                if ($riwayatId == 0) {
                    // Berarti ini pendidikan baru ditambahkan -> INSERT
                    DB::table('pegawai_riwayat_pendidikan')->insert([
                        'id_pegawai'  => $id_pegawai,
                        'jenjang'     => $jenjang[$key] ?? null,
                        'universitas' => $univ_id,
                        'jurusan'     => $prodi_id
                    ]);
                } else {
                    // Berarti ini pendidikan yang sudah ada -> UPDATE
                    DB::table('pegawai_riwayat_pendidikan')
                        ->where('id', $riwayatId)
                        ->update([
                            'jenjang'     => $jenjang[$key] ?? null,
                            'universitas' => $univ_id,
                            'jurusan'     => $prodi_id
                        ]);
                }
            }

            // Jika tidak ada masalah sampai di baris ini, simpan permanen ke DB
            DB::commit();

            // Redirect dengan mengirim session flash data
            return redirect()->route('pegawai.index')->with('status', 'Data pegawai berhasil diperbarui!');

        } catch (\Exception $e) {
            // Jika ada tabel yang gagal diproses, batalkan SEMUA perintah query sebelumnya
            DB::rollBack();

            // Kembalikan ke halaman form sebelumnya, bawa kembali input lama dan pesan errornya
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    //action tambahan 
    public function get_status(Request $request){
        $id = $request->input('id');
        $where = array('id_jenis_pegawai' => $id);
        $r = \DB::table('pegawai_posisi')->where($where)->get();
        return response()->json($r);
    }
    
    public function get_homebase(){
        $data = \DB::table('pegawai_homebase')->get();
        return response()->json($data);
    }

    public function get_prodi(){
        $data = \DB::table('program_studi')->get();
        return response()->json($data);
    }
    public function get_jabatan(Request $request){
        $bagian=$request->bagian;
        $where = array('bagian' => $bagian);
        $data = \DB::table('jabatan_struktural')->where($where)->get();
        return response()->json($data);
    }
    public function lihat_krm(){
        $query['title'] = 'KRM Dosen';
        $query['pegawai'] = Pegawai::join('pegawai_biodata', 'pegawai.id', '=', 'pegawai_biodata.id_pegawai')->get();
        $query['CurrentPage'] = 'content';
        return view('admin.pegawai.lihat_krm', $query);
    }
    public function show_krm(Request $request){
        $query['title'] = 'KRM Dosen';
        $query['pegawai'] = Pegawai::with('biodata')->find($request->id);
        $query['jadwal'] = MasterJadwalTemp::join('master_mata_kuliah', 'master_jadwal_temp.kode_mata_kuliah', '=', 'master_mata_kuliah.kode_mata_kuliah')
            ->where('id_dosen',$request->id)->orWhere('id_dosen2',$request->id)->get();
        $query['CurrentPage'] = 'content';
        return view('admin.pegawai.show_krm', $query);
    }
    public function struktur(){
        $query['title'] = "Struktur Pegawai";
        $query['CurrentPage'] = 'content';
        $query['s'] = DB::table('struktur_pegawai')->first();
        $query['s2'] = DB::table('struktur_pegawai2')->first();
        $query['npp'] = DB::table('pegawai as p')
                            ->select('p.id', 'p.npp', 'p.nama')
                            ->leftJoin('pegawai_biodata as b', 'b.id_pegawai', '=', 'p.id')
                            ->leftJoin('pegawai_golongan as g', 'g.id_pegawai', '=', 'p.id')
                            ->leftJoin('pegawai_jabatan_fungsional as f', 'f.id_pegawai', '=', 'p.id')
                            ->leftJoin('pegawai_jabatan_struktural as s', 's.id_pegawai', '=', 'p.id')
                            ->whereNotIn('p.id', [1, 11111111]) 
                            ->groupBy('p.id', 'p.npp', 'p.nama') 
                            ->get(); 
        return view('admin.pegawai.struktur', $query);
    }
    public function save_struktur(Request $request)
    {
        // 1. Pecah string dari input form menjadi array
        // Menggunakan "?? ''" (Null Coalescing) untuk mencegah error jika input kosong
        $ketua_st              = explode("-", $request->input('ketua_st') ?? '');
        $pembantu_1            = explode("-", $request->input('pembantu_1') ?? '');
        $pembantu_2            = explode("-", $request->input('pembantu_2') ?? '');
        $pembantu_3            = explode("-", $request->input('pembantu_3') ?? '');
        $prodi_d3              = explode("-", $request->input('prodi_d3') ?? '');
        $prodi_s1              = explode("-", $request->input('prodi_s1') ?? '');
        $sekertaris_prodi_d3   = explode("-", $request->input('sekertaris_prodi_d3') ?? '');
        $sekertaris_prodi_s1   = explode("-", $request->input('sekertaris_prodi_s1') ?? '');
        $ketua_mutu            = explode("-", $request->input('ketua_mutu') ?? '');
        $ketua_penelitian      = explode("-", $request->input('ketua_penelitian') ?? '');
        $sekertaris_penelitian = explode("-", $request->input('sekertaris_penelitian') ?? '');
        $koor_lab              = explode("-", $request->input('koor_lab') ?? '');
        $koor_sarana           = explode("-", $request->input('koor_sarana') ?? '');

        // 2. Mulai Database Transaction untuk keamanan
        try {
            DB::beginTransaction();

            // === UPDATE TABEL struktur_pegawai ===
            // Fungsi end() akan mengambil nilai (NAMA/JABATAN) dari elemen terakhir array
            $dataStruktur1 = [
                'ketua_st'              => end($ketua_st),
                'pembantu_1'            => end($pembantu_1),
                'pembantu_2'            => end($pembantu_2),
                'pembantu_3'            => end($pembantu_3),
                'prodi_d3'              => end($prodi_d3),
                'prodi_s1'              => end($prodi_s1),
                'sekertaris_prodi_d3'   => end($sekertaris_prodi_d3),
                'sekertaris_prodi_s1'   => end($sekertaris_prodi_s1),
                'ketua_mutu'            => end($ketua_mutu),
                'ketua_penelitian'      => end($ketua_penelitian),
                'sekertaris_penelitian' => end($sekertaris_penelitian),
                'koor_lab'              => end($koor_lab),
                'koor_sarana'           => end($koor_sarana)
            ];

            DB::table('struktur_pegawai')->where('id', 1)->update($dataStruktur1);

            // === UPDATE TABEL jabatan_new (Looping) ===
            // Fungsi reset() akan mengambil nilai (ID) dari elemen pertama array
            $list_pegawai = [
                reset($ketua_st),
                reset($pembantu_1),
                reset($pembantu_2),
                reset($pembantu_3),
                reset($prodi_d3),
                reset($prodi_s1),
                reset($sekertaris_prodi_d3),
                reset($sekertaris_prodi_s1),
                reset($ketua_mutu),
                reset($ketua_penelitian),
                reset($sekertaris_penelitian),
                reset($koor_lab),
                reset($koor_sarana),
            ];

            // Mengambil semua data dari jabatan_new (pastikan urutannya di DB konsisten)
            $struktur_new = DB::table('jabatan_new')->orderBy('id', 'asc')->get();
            
            $i = 0;
            foreach ($struktur_new as $row) {
                // Pastikan index array $list_pegawai tersedia
                if (isset($list_pegawai[$i])) {
                    DB::table('jabatan_new')
                        ->where('id', $row->id)
                        ->update(['id_pegawai' => $list_pegawai[$i]]);
                }
                $i++;
            }

            // === UPDATE TABEL struktur_pegawai2 ===
            $dataStruktur2 = [
                'ketua_st'              => reset($ketua_st),
                'pembantu_1'            => reset($pembantu_1),
                'pembantu_2'            => reset($pembantu_2),
                'pembantu_3'            => reset($pembantu_3),
                'prodi_d3'              => reset($prodi_d3),
                'prodi_s1'              => reset($prodi_s1),
                'sekertaris_prodi_d3'   => reset($sekertaris_prodi_d3),
                'sekertaris_prodi_s1'   => reset($sekertaris_prodi_s1),
                'ketua_mutu'            => reset($ketua_mutu),
                'ketua_penelitian'      => reset($ketua_penelitian),
                'sekertaris_penelitian' => reset($sekertaris_penelitian),
                'koor_lab'              => reset($koor_lab),
                'koor_sarana'           => reset($koor_sarana)
            ];

            DB::table('struktur_pegawai2')->where('id', 1)->update($dataStruktur2);

            // Komit jika semua berhasil
            DB::commit();

            // Mengarahkan kembali ke halaman struktur (Gunakan nama route jika sudah ada)
            // return redirect()->route('pegawai.struktur')->with('success', 'Struktur berhasil diperbarui.');
            return redirect('pegawai/struktur')->with('success', 'Struktur berhasil diperbarui.');

        } catch (\Exception $e) {
            // Batalkan semua query jika terjadi error
            DB::rollBack();
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
