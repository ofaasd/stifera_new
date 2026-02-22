<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RolesOld
 * 
 * @property int $id
 * @property string $nama
 * @property int $is_active
 * @property Carbon $log
 *
 * @package App\Models
 */
class RolesOld extends Model
{
	protected $table = 'roles_old';
	public $timestamps = false;

	protected $casts = [
		'is_active' => 'int',
		'log' => 'datetime'
	];

	protected $fillable = [
		'nama',
		'is_active',
		'log'
	];
}
