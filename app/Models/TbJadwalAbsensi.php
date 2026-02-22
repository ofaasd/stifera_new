<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TbJadwalAbsensi
 * 
 * @property int $id
 * @property int $id_pegawai
 * @property Carbon $jam_masuk
 * @property Carbon $jam_keluar
 * @property string $id_fingerprint
 * @property int $id_ta
 *
 * @package App\Models
 */
class TbJadwalAbsensi extends Model
{
	protected $table = 'tb_jadwal_absensi';
	public $timestamps = false;

	protected $casts = [
		'id_pegawai' => 'int',
		'jam_masuk' => 'datetime',
		'jam_keluar' => 'datetime',
		'id_ta' => 'int'
	];

	protected $fillable = [
		'id_pegawai',
		'jam_masuk',
		'jam_keluar',
		'id_fingerprint',
		'id_ta'
	];
}
