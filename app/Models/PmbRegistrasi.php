<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PmbRegistrasi
 * 
 * @property int $id
 * @property int $nopen
 * @property string|null $spi
 * @property int|null $operasional
 * @property string|null $potongan_spi
 * @property string|null $sks
 * @property int|null $kemahasiswaan
 * @property int|null $seragam
 * @property string|null $jml_tahap_1
 * @property Carbon|null $tgl_tahap_1
 * @property int|null $status_tahap_1
 * @property string|null $jml_tahap_2
 * @property Carbon|null $tgl_tahap_2
 * @property int|null $status_tahap_2
 * @property string|null $jml_tahap_3
 * @property Carbon|null $tgl_tahap_3
 * @property int|null $status_tahap_3
 * @property string|null $jml_tahap_4
 * @property Carbon|null $tgl_tahap_4
 * @property int|null $status_tahap_4
 * @property Carbon $log
 *
 * @package App\Models
 */
class PmbRegistrasi extends Model
{
	protected $table = 'pmb_registrasi';
	public $timestamps = false;

	protected $casts = [
		'nopen' => 'int',
		'operasional' => 'int',
		'kemahasiswaan' => 'int',
		'seragam' => 'int',
		'tgl_tahap_1' => 'datetime',
		'status_tahap_1' => 'int',
		'tgl_tahap_2' => 'datetime',
		'status_tahap_2' => 'int',
		'tgl_tahap_3' => 'datetime',
		'status_tahap_3' => 'int',
		'tgl_tahap_4' => 'datetime',
		'status_tahap_4' => 'int',
		'log' => 'datetime'
	];

	protected $fillable = [
		'nopen',
		'spi',
		'operasional',
		'potongan_spi',
		'sks',
		'kemahasiswaan',
		'seragam',
		'jml_tahap_1',
		'tgl_tahap_1',
		'status_tahap_1',
		'jml_tahap_2',
		'tgl_tahap_2',
		'status_tahap_2',
		'jml_tahap_3',
		'tgl_tahap_3',
		'status_tahap_3',
		'jml_tahap_4',
		'tgl_tahap_4',
		'status_tahap_4',
		'log'
	];
}
