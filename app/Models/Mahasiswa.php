<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mahasiswa
 * 
 * @property int $id
 * @property string $nim
 * @property string|null $nama
 * @property string|null $no_ktp
 * @property int $jk
 * @property int $agama
 * @property string|null $tempat_lahir
 * @property Carbon|null $tgl_lahir
 * @property string|null $nama_ibu
 * @property string|null $nama_ayah
 * @property string|null $hp_ortu
 * @property string|null $alamat
 * @property string|null $alamat_semarang
 * @property string|null $rt
 * @property string|null $rw
 * @property string|null $kelurahan
 * @property string|null $kecamatan
 * @property string|null $kokab
 * @property string|null $provinsi
 * @property string|null $telp
 * @property string|null $hp
 * @property string|null $email
 * @property string|null $paswd
 * @property int $status
 * @property string|null $foto_mhs
 * @property string|null $nims
 * @property bool $kelas
 * @property int|null $angkatan
 * @property int|null $ta
 * @property int $id_program_studi
 * @property int $id_dsn_wali
 * @property Carbon|null $log_date
 * @property int $tipe_mhs
 *
 * @package App\Models
 */
class Mahasiswa extends Model
{
	protected $table = 'mahasiswa';
	public $timestamps = false;

	protected $casts = [
		'jk' => 'int',
		'agama' => 'int',
		'tgl_lahir' => 'datetime',
		'status' => 'int',
		'kelas' => 'bool',
		'angkatan' => 'int',
		'ta' => 'int',
		'id_program_studi' => 'int',
		'id_dsn_wali' => 'int',
		'log_date' => 'datetime',
		'tipe_mhs' => 'int'
	];

	protected $fillable = [
		'nim',
		'nama',
		'no_ktp',
		'jk',
		'agama',
		'tempat_lahir',
		'tgl_lahir',
		'nama_ibu',
		'nama_ayah',
		'hp_ortu',
		'alamat',
		'alamat_semarang',
		'rt',
		'rw',
		'kelurahan',
		'kecamatan',
		'kokab',
		'provinsi',
		'telp',
		'hp',
		'email',
		'paswd',
		'status',
		'foto_mhs',
		'nims',
		'kelas',
		'angkatan',
		'ta',
		'id_program_studi',
		'id_dsn_wali',
		'log_date',
		'tipe_mhs'
	];
}
