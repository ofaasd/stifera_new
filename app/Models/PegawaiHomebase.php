<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiHomebase
 * 
 * @property int $id
 * @property string $nama_jurusan
 *
 * @package App\Models
 */
class PegawaiHomebase extends Model
{
	protected $table = 'pegawai_homebase';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'id',
		'nama_jurusan'
	];
}
