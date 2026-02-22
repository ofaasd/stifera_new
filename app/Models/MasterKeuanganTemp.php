<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKeuanganTemp
 * 
 * @property int $id
 * @property int $id_tahun
 * @property int $nopen
 * @property string $nim
 * @property int $operasional
 * @property int $seragam
 * @property int $kemahasiswaan
 * @property int $spi
 * @property int|null $potongan_spi
 * @property int $sks
 * @property int $jml_tahap_1
 * @property Carbon $tgl_tahap_1
 * @property int $status_tahap_1
 * @property int $jml_tahap_2
 * @property Carbon $tgl_tahap_2
 * @property int $status_tahap_2
 * @property int $jml_tahap_3
 * @property Carbon $tgl_tahap_3
 * @property int $status_tahap_3
 * @property int $total
 * @property int|null $potongan
 * @property int $is_publish
 * @property int $is_bayar
 * @property Carbon $log_date
 * @property int|null $izin_ujian
 *
 * @package App\Models
 */
class MasterKeuanganTemp extends Model
{
	protected $table = 'master_keuangan_temp';
	public $timestamps = false;

	protected $casts = [
		'id_tahun' => 'int',
		'nopen' => 'int',
		'operasional' => 'int',
		'seragam' => 'int',
		'kemahasiswaan' => 'int',
		'spi' => 'int',
		'potongan_spi' => 'int',
		'sks' => 'int',
		'jml_tahap_1' => 'int',
		'tgl_tahap_1' => 'datetime',
		'status_tahap_1' => 'int',
		'jml_tahap_2' => 'int',
		'tgl_tahap_2' => 'datetime',
		'status_tahap_2' => 'int',
		'jml_tahap_3' => 'int',
		'tgl_tahap_3' => 'datetime',
		'status_tahap_3' => 'int',
		'total' => 'int',
		'potongan' => 'int',
		'is_publish' => 'int',
		'is_bayar' => 'int',
		'log_date' => 'datetime',
		'izin_ujian' => 'int'
	];

	protected $fillable = [
		'id_tahun',
		'nopen',
		'nim',
		'operasional',
		'seragam',
		'kemahasiswaan',
		'spi',
		'potongan_spi',
		'sks',
		'jml_tahap_1',
		'tgl_tahap_1',
		'status_tahap_1',
		'jml_tahap_2',
		'tgl_tahap_2',
		'status_tahap_2',
		'jml_tahap_3',
		'tgl_tahap_3',
		'status_tahap_3',
		'total',
		'potongan',
		'is_publish',
		'is_bayar',
		'log_date',
		'izin_ujian'
	];
}
