<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblJadwalUjian
 * 
 * @property int $id
 * @property int|null $id_jadwal
 * @property Carbon|null $tanggal_uts_t
 * @property int|null $id_jam_uts_t
 * @property Carbon|null $tanggal_uas_t
 * @property int|null $id_jam_uas_t
 * @property Carbon|null $tanggal_uts_p
 * @property int|null $id_jam_uts_p
 * @property Carbon|null $tanggal_uas_p
 * @property int|null $id_jam_uas_p
 * @property int|null $ta
 * @property Carbon|null $log
 *
 * @package App\Models
 */
class TblJadwalUjian extends Model
{
	protected $table = 'tbl_jadwal_ujian';
	public $timestamps = false;

	protected $casts = [
		'id_jadwal' => 'int',
		'tanggal_uts_t' => 'datetime',
		'id_jam_uts_t' => 'int',
		'tanggal_uas_t' => 'datetime',
		'id_jam_uas_t' => 'int',
		'tanggal_uts_p' => 'datetime',
		'id_jam_uts_p' => 'int',
		'tanggal_uas_p' => 'datetime',
		'id_jam_uas_p' => 'int',
		'ta' => 'int',
		'log' => 'datetime'
	];

	protected $fillable = [
		'id_jadwal',
		'tanggal_uts_t',
		'id_jam_uts_t',
		'tanggal_uas_t',
		'id_jam_uas_t',
		'tanggal_uts_p',
		'id_jam_uts_p',
		'tanggal_uas_p',
		'id_jam_uas_p',
		'ta',
		'log'
	];
}
