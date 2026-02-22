<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class KartuUjian
 * 
 * @property int $id
 * @property int $ta_id
 * @property Carbon $created_at
 * @property Carbon $tanggal_uts
 * @property Carbon $tanggal_uas
 *
 * @package App\Models
 */
class KartuUjian extends Model
{
	protected $table = 'kartu_ujian';
	public $timestamps = false;

	protected $casts = [
		'ta_id' => 'int',
		'tanggal_uts' => 'datetime',
		'tanggal_uas' => 'datetime'
	];

	protected $fillable = [
		'ta_id',
		'tanggal_uts',
		'tanggal_uas'
	];
}
