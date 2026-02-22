<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblTempatUjian
 * 
 * @property int $id
 * @property int $ta
 * @property int $id_jadwal
 * @property string $nim
 * @property int $no_kursi
 * @property string $ruang
 * @property Carbon $log
 *
 * @package App\Models
 */
class TblTempatUjian extends Model
{
	protected $table = 'tbl_tempat_ujian';
	public $timestamps = false;

	protected $casts = [
		'ta' => 'int',
		'id_jadwal' => 'int',
		'no_kursi' => 'int',
		'log' => 'datetime'
	];

	protected $fillable = [
		'ta',
		'id_jadwal',
		'nim',
		'no_kursi',
		'ruang',
		'log'
	];
}
