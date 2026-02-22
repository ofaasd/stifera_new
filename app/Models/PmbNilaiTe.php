<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PmbNilaiTe
 * 
 * @property int $id
 * @property int $nopen
 * @property int|null $total_score
 *
 * @package App\Models
 */
class PmbNilaiTe extends Model
{
	protected $table = 'pmb_nilai_tes';
	public $timestamps = false;

	protected $casts = [
		'nopen' => 'int',
		'total_score' => 'int'
	];

	protected $fillable = [
		'nopen',
		'total_score'
	];
}
