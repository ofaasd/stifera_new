<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblYudisium
 * 
 * @property int $id
 * @property string $nim
 * @property Carbon $tahun
 * @property Carbon $tanggal
 *
 * @package App\Models
 */
class TblYudisium extends Model
{
	protected $table = 'tbl_yudisium';
	public $timestamps = false;

	protected $casts = [
		'tahun' => 'datetime',
		'tanggal' => 'datetime'
	];

	protected $fillable = [
		'nim',
		'tahun',
		'tanggal'
	];
}
