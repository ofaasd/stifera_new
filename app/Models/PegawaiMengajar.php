<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiMengajar
 * 
 * @property int $id
 * @property int $id_pegawai
 * @property int $tahun
 * @property string $institusi
 * @property string $prodi
 * @property string $mata_kuliah
 * @property string $rombel
 * @property int $kelas
 * @property int $sks
 * @property string $dokumen
 * @property string $sk_mengajar
 *
 * @package App\Models
 */
class PegawaiMengajar extends Model
{
	protected $table = 'pegawai_mengajar';
	public $timestamps = false;

	protected $casts = [
		'id_pegawai' => 'int',
		'tahun' => 'int',
		'kelas' => 'int',
		'sks' => 'int'
	];

	protected $fillable = [
		'id_pegawai',
		'tahun',
		'institusi',
		'prodi',
		'mata_kuliah',
		'rombel',
		'kelas',
		'sks',
		'dokumen',
		'sk_mengajar'
	];
}
