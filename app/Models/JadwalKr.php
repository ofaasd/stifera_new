<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class JadwalKr
 * 
 * @property int $id
 * @property int $status
 * @property Carbon $upadate_at
 *
 * @package App\Models
 */
class JadwalKr extends Model
{
	protected $table = 'jadwal_krs';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'status' => 'int',
		'upadate_at' => 'datetime'
	];

	protected $fillable = [
		'id',
		'status',
		'upadate_at'
	];
}
