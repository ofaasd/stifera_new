<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiJeni
 * 
 * @property int $id
 * @property string $nama
 *
 * @package App\Models
 */
class PegawaiJeni extends Model
{
	protected $table = 'pegawai_jenis';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'nama'
	];
}
