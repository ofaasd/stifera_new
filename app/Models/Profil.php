<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Profil
 * 
 * @property int $id
 * @property int $id_user
 * @property string $jenis_kelamin
 * @property string $tempat_lahir
 * @property Carbon $tanggal_lahir
 * @property int $nisn
 * @property string $alamat
 * @property string $no_telp
 * @property string $nama_ayah
 * @property string $pekerjaan_ayah
 * @property string $nama_ibu
 * @property string $pekerjaan_ibu
 * @property string $tahun_masuk
 * @property string $tahun_lulus
 * @property string $no_ijazah
 * @property string $no_skhun
 *
 * @package App\Models
 */
class Profil extends Model
{
	protected $table = 'profil';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'id_user' => 'int',
		'tanggal_lahir' => 'datetime',
		'nisn' => 'int'
	];

	protected $fillable = [
		'id_user',
		'jenis_kelamin',
		'tempat_lahir',
		'tanggal_lahir',
		'nisn',
		'alamat',
		'no_telp',
		'nama_ayah',
		'pekerjaan_ayah',
		'nama_ibu',
		'pekerjaan_ibu',
		'tahun_masuk',
		'tahun_lulus',
		'no_ijazah',
		'no_skhun'
	];
}
