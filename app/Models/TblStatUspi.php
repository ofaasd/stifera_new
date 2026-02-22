<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblStatUspi
 * 
 * @property int $id
 * @property int $nopen
 * @property int $tahap_1
 * @property int $tahap_2
 * @property int $tahap_3
 * @property Carbon $create_on
 *
 * @package App\Models
 */
class TblStatUspi extends Model
{
	protected $table = 'tbl_stat_uspi';
	public $timestamps = false;

	protected $casts = [
		'nopen' => 'int',
		'tahap_1' => 'int',
		'tahap_2' => 'int',
		'tahap_3' => 'int',
		'create_on' => 'datetime'
	];

	protected $fillable = [
		'nopen',
		'tahap_1',
		'tahap_2',
		'tahap_3',
		'create_on'
	];
}
