<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterPresensi
 * 
 * @property int $id
 * @property int $id_jadwal
 * @property string $nim
 * @property Carbon|null $tgl_pertemuan
 * @property int|null $status
 * @property Carbon $log_date
 * @property string|null $ttd
 *
 * @package App\Models
 */
class MasterPresensi extends Model
{
	protected $table = 'master_presensi';
	public $timestamps = false;

	protected $casts = [
		'id_jadwal' => 'int',
		'tgl_pertemuan' => 'datetime',
		'status' => 'int',
		'log_date' => 'datetime'
	];

	protected $fillable = [
		'id_jadwal',
		'nim',
		'tgl_pertemuan',
		'status',
		'log_date',
		'ttd'
	];
}
