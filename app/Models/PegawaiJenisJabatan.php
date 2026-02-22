<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiJenisJabatan
 * 
 * @property int $id
 * @property int $id_unit_kerja
 * @property string $nama_jenis
 *
 * @package App\Models
 */
class PegawaiJenisJabatan extends Model
{
	protected $table = 'pegawai_jenis_jabatan';
	public $timestamps = false;

	protected $casts = [
		'id_unit_kerja' => 'int'
	];

	protected $fillable = [
		'id_unit_kerja',
		'nama_jenis'
	];
}
