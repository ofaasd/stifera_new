<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PmbNilaiRapor
 * 
 * @property int $id
 * @property int|null $id_user
 * @property int|null $nopen
 * @property float|null $nilai_smt1
 * @property string|null $file_smt1
 * @property float|null $nilai_smt2
 * @property string|null $file_smt2
 * @property float|null $nilai_smt3
 * @property string|null $file_smt3
 * @property float|null $nilai_smt4
 * @property string|null $file_smt4
 * @property float|null $nilai_smt5
 * @property string|null $file_smt5
 *
 * @package App\Models
 */
class PmbNilaiRapor extends Model
{
	protected $table = 'pmb_nilai_rapor';
	public $timestamps = false;

	protected $casts = [
		'id_user' => 'int',
		'nopen' => 'int',
		'nilai_smt1' => 'float',
		'nilai_smt2' => 'float',
		'nilai_smt3' => 'float',
		'nilai_smt4' => 'float',
		'nilai_smt5' => 'float'
	];

	protected $fillable = [
		'id_user',
		'nopen',
		'nilai_smt1',
		'file_smt1',
		'nilai_smt2',
		'file_smt2',
		'nilai_smt3',
		'file_smt3',
		'nilai_smt4',
		'file_smt4',
		'nilai_smt5',
		'file_smt5'
	];
}
