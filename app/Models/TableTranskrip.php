<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TableTranskrip
 * 
 * @property int $id
 * @property int $id_jadwal
 * @property string $nim
 * @property string|null $grade
 * @property string|null $nakhir
 * @property Carbon $log_date
 *
 * @package App\Models
 */
class TableTranskrip extends Model
{
	protected $table = 'table_transkrip';
	public $timestamps = false;

	protected $casts = [
		'id_jadwal' => 'int',
		'log_date' => 'datetime'
	];

	protected $fillable = [
		'id_jadwal',
		'nim',
		'grade',
		'nakhir',
		'log_date'
	];
}
