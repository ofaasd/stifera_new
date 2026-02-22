<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Tagihan
 * 
 * @property int $id
 * @property string $id_mhs
 * @property string $jenis
 * @property int $nominal
 * @property int $ta
 * @property Carbon $log
 * @property int $arsip
 *
 * @package App\Models
 */
class Tagihan extends Model
{
	protected $table = 'tagihan';
	public $timestamps = false;

	protected $casts = [
		'nominal' => 'int',
		'ta' => 'int',
		'log' => 'datetime',
		'arsip' => 'int'
	];

	protected $fillable = [
		'id_mhs',
		'jenis',
		'nominal',
		'ta',
		'log',
		'arsip'
	];
}
