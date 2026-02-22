<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PmbKeuangan
 * 
 * @property int $id
 * @property string $prodi
 * @property int $kelas
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
class PmbKeuangan extends Model
{
	protected $table = 'pmb_keuangan';
	public $timestamps = false;

	protected $casts = [
		'kelas' => 'int',
		'total_spi' => 'int',
		'total_sks' => 'int',
		'operasional' => 'int',
		'kemahasiswaan' => 'int',
		'seragam' => 'int',
		'jml_sks' => 'int',
		'sks' => 'int'
	];

	protected $fillable = [
		'prodi',
		'kelas',
		'total_spi',
		'total_sks',
		'operasional',
		'kemahasiswaan',
		'seragam',
		'jml_sks',
		'sks'
	];
}
