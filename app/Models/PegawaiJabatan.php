<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiJabatan
 * 
 * @property int $id
 * @property int $id_bagian
 * @property int $id_jenis_jabatan
 * @property string $nama_jabatan
 *
 * @package App\Models
 */
class PegawaiJabatan extends Model
{
	protected $table = 'pegawai_jabatan';
	public $timestamps = false;

	protected $casts = [
		'id_bagian' => 'int',
		'id_jenis_jabatan' => 'int'
	];

	protected $fillable = [
		'id_bagian',
		'id_jenis_jabatan',
		'nama_jabatan'
	];
}
