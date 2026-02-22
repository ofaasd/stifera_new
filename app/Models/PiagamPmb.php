<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PiagamPmb
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $nopen
 * @property string|null $file1
 * @property string|null $ket1
 * @property string|null $file2
 * @property string|null $ket2
 * @property string|null $file3
 * @property string|null $ket3
 * @property Carbon $log
 *
 * @package App\Models
 */
class PiagamPmb extends Model
{
	protected $table = 'piagam_pmb';
	public $timestamps = false;

	protected $casts = [
		'user_id' => 'int',
		'nopen' => 'int',
		'log' => 'datetime'
	];

	protected $fillable = [
		'user_id',
		'nopen',
		'file1',
		'ket1',
		'file2',
		'ket2',
		'file3',
		'ket3',
		'log'
	];
}
