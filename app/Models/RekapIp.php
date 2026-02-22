<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RekapIp
 * 
 * @property int $id
 * @property string $id_mhs
 * @property int $id_ta
 * @property string $ips
 * @property Carbon $log
 *
 * @package App\Models
 */
class RekapIp extends Model
{
	protected $table = 'rekap_ips';
	public $timestamps = false;

	protected $casts = [
		'id_ta' => 'int',
		'log' => 'datetime'
	];

	protected $fillable = [
		'id_mhs',
		'id_ta',
		'ips',
		'log'
	];
}
