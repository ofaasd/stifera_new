<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConvertNilai
 * 
 * @property int $id
 * @property string $huruf
 * @property float $nilai
 *
 * @package App\Models
 */
class ConvertNilai extends Model
{
	protected $table = 'convert_nilai';
	public $timestamps = false;

	protected $casts = [
		'nilai' => 'float'
	];

	protected $fillable = [
		'huruf',
		'nilai'
	];
}
