<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Absensi
 * 
 * @property int $id
 * @property string $pin
 * @property string $nama
 * @property string|null $jabatan
 * @property Carbon|null $tanggal
 * @property string|null $nama_shift
 * @property Carbon|null $masuk
 * @property Carbon|null $scan1
 * @property Carbon|null $terlambat
 * @property Carbon|null $keluar
 * @property Carbon|null $scan2
 * @property Carbon|null $p_cepat
 * @property int|null $nilai
 * @property Carbon|null $durasi
 * @property Carbon|null $total_ot
 * @property string|null $keterangan
 *
 * @package App\Models
 */
class Absensi extends Model
{
	protected $table = 'absensi';
	public $timestamps = false;

	protected $casts = [
		'tanggal' => 'datetime',
		'masuk' => 'datetime',
		'scan1' => 'datetime',
		'terlambat' => 'datetime',
		'keluar' => 'datetime',
		'scan2' => 'datetime',
		'p_cepat' => 'datetime',
		'nilai' => 'int',
		'durasi' => 'datetime',
		'total_ot' => 'datetime'
	];

	protected $fillable = [
		'pin',
		'nama',
		'jabatan',
		'tanggal',
		'nama_shift',
		'masuk',
		'scan1',
		'terlambat',
		'keluar',
		'scan2',
		'p_cepat',
		'nilai',
		'durasi',
		'total_ot',
		'keterangan'
	];
}
