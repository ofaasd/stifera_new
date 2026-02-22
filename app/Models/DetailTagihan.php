<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DetailTagihan
 * 
 * @property int $id
 * @property int $id_ta
 * @property string $nim
 * @property int|null $total
 * @property Carbon|null $tgl
 * @property int|null $terbayar
 * @property int|null $sisa
 * @property int|null $status
 * @property Carbon $log
 * @property int $arsip
 *
 * @package App\Models
 */
class DetailTagihan extends Model
{
	protected $table = 'detail_tagihan';
	public $timestamps = false;

	protected $casts = [
		'id_ta' => 'int',
		'total' => 'int',
		'tgl' => 'datetime',
		'terbayar' => 'int',
		'sisa' => 'int',
		'status' => 'int',
		'log' => 'datetime',
		'arsip' => 'int'
	];

	protected $fillable = [
		'id_ta',
		'nim',
		'total',
		'tgl',
		'terbayar',
		'sisa',
		'status',
		'log',
		'arsip'
	];
}
