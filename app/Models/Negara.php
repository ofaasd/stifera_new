<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Negara
 * 
 * @property string $id_negara
 * @property string|null $nm_negara
 * @property float|null $a_ln
 *
 * @package App\Models
 */
class Negara extends Model
{
	protected $table = 'negara';
	protected $primaryKey = 'id_negara';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'a_ln' => 'float'
	];

	protected $fillable = [
		'nm_negara',
		'a_ln'
	];
}
