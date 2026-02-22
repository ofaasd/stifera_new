<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblRombel
 * 
 * @property int $id
 * @property string $rombel
 * @property string $is_aktif
 * @property Carbon $create_on
 *
 * @package App\Models
 */
class TblRombel extends Model
{
	protected $table = 'tbl_rombel';
	public $timestamps = false;

	protected $casts = [
		'create_on' => 'datetime'
	];

	protected $fillable = [
		'rombel',
		'is_aktif',
		'create_on'
	];
}
