<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKeuanganPokok
 * 
 * @property int $id
 * @property int $id_mahasiswa
 * @property int $total_spi
 * @property int $total_sks
 * @property int $operasional
 * @property int $kemahasiswaan
 * @property int $seragam
 * @property int $jml_sks
 * @property int $sks
 *
 * @package App\Models
 */
class MasterKeuanganPokok extends Model
{
	protected $table = 'master_keuangan_pokok';
	public $timestamps = false;

	protected $casts = [
		'id_mahasiswa' => 'int',
		'total_spi' => 'int',
		'total_sks' => 'int',
		'operasional' => 'int',
		'kemahasiswaan' => 'int',
		'seragam' => 'int',
		'jml_sks' => 'int',
		'sks' => 'int'
	];

	protected $fillable = [
		'id_mahasiswa',
		'total_spi',
		'total_sks',
		'operasional',
		'kemahasiswaan',
		'seragam',
		'jml_sks',
		'sks'
	];
}
