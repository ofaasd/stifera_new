<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterIpOld
 * 
 * @property int $id
 * @property string $nim
 * @property int $id_ta
 * @property float $ip
 *
 * @package App\Models
 */
class MasterIpOld extends Model
{
	protected $table = 'master_ip_old';
	public $timestamps = false;

	protected $casts = [
		'id_ta' => 'int',
		'ip' => 'float'
	];

	protected $fillable = [
		'nim',
		'id_ta',
		'ip'
	];
}
