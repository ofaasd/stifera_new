<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Testimoni
 * 
 * @property int $id_testimoni
 * @property int $id_user
 * @property string $testimoni
 * @property string $is_tampil
 *
 * @package App\Models
 */
class Testimoni extends Model
{
	protected $table = 'testimoni';
	protected $primaryKey = 'id_testimoni';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_testimoni' => 'int',
		'id_user' => 'int'
	];

	protected $fillable = [
		'id_user',
		'testimoni',
		'is_tampil'
	];
}
