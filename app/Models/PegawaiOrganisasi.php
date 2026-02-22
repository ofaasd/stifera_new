<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiOrganisasi
 * 
 * @property int $id
 * @property int $id_pegawai
 * @property string $nama_organisasi
 * @property string $jabatan
 * @property Carbon $tahun
 * @property Carbon $tahun_keluar
 * @property int $sekarang
 *
 * @package App\Models
 */
class PegawaiOrganisasi extends Model
{
	protected $table = 'pegawai_organisasi';
	public $timestamps = false;

	protected $casts = [
		'id_pegawai' => 'int',
		'tahun' => 'datetime',
		'tahun_keluar' => 'datetime',
		'sekarang' => 'int'
	];

	protected $fillable = [
		'id_pegawai',
		'nama_organisasi',
		'jabatan',
		'tahun',
		'tahun_keluar',
		'sekarang'
	];
}
