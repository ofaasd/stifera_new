<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterUniversita
 * 
 * @property int $id
 * @property string $nama_universitas
 *
 * @package App\Models
 */
class MasterUniversita extends Model
{
	protected $table = 'master_universitas';
	public $timestamps = false;

	protected $fillable = [
		'nama_universitas'
	];
}
