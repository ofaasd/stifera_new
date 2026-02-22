<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiAnggotaPengabdian
 * 
 * @property int $id
 * @property int $jenis_anggota
 * @property string $id_anggota
 * @property int $id_pengabdian
 *
 * @package App\Models
 */
class PegawaiAnggotaPengabdian extends Model
{
	protected $table = 'pegawai_anggota_pengabdian';
	public $timestamps = false;

	protected $casts = [
		'jenis_anggota' => 'int',
		'id_pengabdian' => 'int'
	];

	protected $fillable = [
		'jenis_anggota',
		'id_anggota',
		'id_pengabdian'
	];
}
