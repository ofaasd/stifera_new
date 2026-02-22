<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiHaki
 * 
 * @property int $id
 * @property int $id_pegawai
 * @property string $pemilik
 * @property int $tahun_ajaran
 * @property string $judul
 * @property string $sertifikat
 *
 * @package App\Models
 */
class PegawaiHaki extends Model
{
	protected $table = 'pegawai_haki';
	public $timestamps = false;

	protected $casts = [
		'id_pegawai' => 'int',
		'tahun_ajaran' => 'int'
	];

	protected $fillable = [
		'id_pegawai',
		'pemilik',
		'tahun_ajaran',
		'judul',
		'sertifikat'
	];
}
