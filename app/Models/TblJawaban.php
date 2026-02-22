<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblJawaban
 * 
 * @property int $id
 * @property int|null $id_soal
 * @property string|null $jawaban
 * @property int|null $nopen
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class TblJawaban extends Model
{
	protected $table = 'tbl_jawaban';
	public $timestamps = false;

	protected $casts = [
		'id_soal' => 'int',
		'nopen' => 'int'
	];

	protected $fillable = [
		'id_soal',
		'jawaban',
		'nopen'
	];
}
