<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class StatusAlumnus
 * 
 * @property int $id
 * @property int $id_user
 * @property string $status
 * @property string $deskripsi
 *
 * @package App\Models
 */
class StatusAlumnus extends Model
{
	protected $table = 'status_alumni';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'id_user' => 'int'
	];

	protected $fillable = [
		'id_user',
		'status',
		'deskripsi'
	];
}
