<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblMemo
 * 
 * @property int $id
 * @property int $id_pertemuan
 * @property string $memo
 * @property string|null $sub
 * @property int $mhs_hadir
 * @property int $mhs_tidak_hadir
 * @property Carbon $log
 *
 * @package App\Models
 */
class TblMemo extends Model
{
	protected $table = 'tbl_memo';
	public $timestamps = false;

	protected $casts = [
		'id_pertemuan' => 'int',
		'mhs_hadir' => 'int',
		'mhs_tidak_hadir' => 'int',
		'log' => 'datetime'
	];

	protected $fillable = [
		'id_pertemuan',
		'memo',
		'sub',
		'mhs_hadir',
		'mhs_tidak_hadir',
		'log'
	];
}
