<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiAnggotaPenelitian
 * 
 * @property int $id
 * @property int $jenis_anggota
 * @property string $id_anggota
 * @property int $id_penelitian
 *
 * @package App\Models
 */
class PegawaiAnggotaPenelitian extends Model
{
	protected $table = 'pegawai_anggota_penelitian';
	public $timestamps = false;

	protected $casts = [
		'jenis_anggota' => 'int',
		'id_penelitian' => 'int'
	];

	protected $fillable = [
		'jenis_anggota',
		'id_anggota',
		'id_penelitian'
	];
}
