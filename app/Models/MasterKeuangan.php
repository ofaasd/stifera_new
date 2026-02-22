<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKeuangan
 * 
 * @property int $id
 * @property int $id_tahun
 * @property int $nim
 * @property int $operasional
 * @property int $seragam
 * @property int $kemahasiswaan
 * @property int $spi
 * @property int $sks
 * @property int $total
 * @property int $is_publish
 * @property int $is_bayar
 * @property Carbon $log_date
 *
 * @package App\Models
 */
class MasterKeuangan extends Model
{
	protected $table = 'master_keuangan';
	public $timestamps = false;

	protected $casts = [
		'id_tahun' => 'int',
		'nim' => 'int',
		'operasional' => 'int',
		'seragam' => 'int',
		'kemahasiswaan' => 'int',
		'spi' => 'int',
		'sks' => 'int',
		'total' => 'int',
		'is_publish' => 'int',
		'is_bayar' => 'int',
		'log_date' => 'datetime'
	];

	protected $fillable = [
		'id_tahun',
		'nim',
		'operasional',
		'seragam',
		'kemahasiswaan',
		'spi',
		'sks',
		'total',
		'is_publish',
		'is_bayar',
		'log_date'
	];
}
