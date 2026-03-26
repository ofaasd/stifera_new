<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class Pegawai
 * 
 * @property int $id
 * @property string|null $npp
 * @property string|null $nama
 * @property string|null $usrnm
 * @property string|null $paswd
 * @property int $status
 *
 * @package App\Models
 */
class Pegawai extends Authenticatable
{
	protected $table = 'pegawai';
	public $timestamps = false;

	protected $hidden = [
		'paswd',
	];

	protected $casts = [
		'status' => 'int'
	];

	protected $fillable = [
		'npp',
		'nama',
		'usrnm',
		'paswd',
		'status'
	];
	public function biodata(){
		return $this->hasOne(PegawaiBiodatum::class, 'id_pegawai', 'id');
	}

	public function getAuthPasswordName()
	{
		return 'paswd';
	}
}
