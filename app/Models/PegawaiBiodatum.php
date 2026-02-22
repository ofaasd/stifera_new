<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiBiodatum
 * 
 * @property int $id
 * @property int|null $id_pegawai
 * @property string $kd_posisi_pegawai
 * @property int $id_progdi
 * @property string $ktp
 * @property Carbon $expired_ktp
 * @property string $nidn
 * @property string $nip_pns
 * @property string $nama_lengkap
 * @property string $gelar_depan
 * @property string $gelar_belakang
 * @property string $tempat_lahir
 * @property Carbon $tanggal_lahir
 * @property string $agama
 * @property string|null $jenis_kelamin
 * @property string $alamat
 * @property string $notelp
 * @property string $nohp
 * @property string $nama_pasangan
 * @property int $jumlah_anak
 * @property string $pekerjaan_pasangan
 * @property string $email1
 * @property string $email2
 * @property string $blog
 * @property string $citation
 * @property string $status_pegawai
 * @property string $no_ktp
 * @property string $no_kk
 * @property Carbon|null $tgl_lahir_pasangan
 * @property string|null $foto
 * @property string $no_bpjs_kesehatan
 * @property string $no_bpjs_ketenagakerjaan
 * @property string $status_nikah
 * @property int $homebase
 * @property string $golongan_darah
 * @property string $provinsi
 * @property string $kotakab
 * @property string $kecamatan
 * @property string $kelurahan
 *
 * @package App\Models
 */
class PegawaiBiodatum extends Model
{
	protected $table = 'pegawai_biodata';
	public $timestamps = false;

	protected $casts = [
		'id_pegawai' => 'int',
		'id_progdi' => 'int',
		'expired_ktp' => 'datetime',
		'tanggal_lahir' => 'datetime',
		'jumlah_anak' => 'int',
		'tgl_lahir_pasangan' => 'datetime',
		'homebase' => 'int'
	];

	protected $fillable = [
		'id_pegawai',
		'kd_posisi_pegawai',
		'id_progdi',
		'ktp',
		'expired_ktp',
		'nidn',
		'nip_pns',
		'nama_lengkap',
		'gelar_depan',
		'gelar_belakang',
		'tempat_lahir',
		'tanggal_lahir',
		'agama',
		'jenis_kelamin',
		'alamat',
		'notelp',
		'nohp',
		'nama_pasangan',
		'jumlah_anak',
		'pekerjaan_pasangan',
		'email1',
		'email2',
		'blog',
		'citation',
		'status_pegawai',
		'no_ktp',
		'no_kk',
		'tgl_lahir_pasangan',
		'foto',
		'no_bpjs_kesehatan',
		'no_bpjs_ketenagakerjaan',
		'status_nikah',
		'homebase',
		'golongan_darah',
		'provinsi',
		'kotakab',
		'kecamatan',
		'kelurahan'
	];
}
