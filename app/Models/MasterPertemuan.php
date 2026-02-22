<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterPertemuan
 * 
 * @property int $id
 * @property int $id_pertemuan
 * @property int $id_jadwal
 * @property int $id_tahun
 * @property Carbon $tgl_pertemuan
 * @property string|null $kode_kelas
 * @property Carbon|null $expired_kode
 * @property int $kunci_kehadiran
 *
 * @package App\Models
 */
class MasterPertemuan extends Model
{
	protected $table = 'master_pertemuan';
	public $timestamps = false;

	protected $casts = [
		'id_pertemuan' => 'int',
		'id_jadwal' => 'int',
		'id_tahun' => 'int',
		'tgl_pertemuan' => 'datetime',
		'expired_kode' => 'datetime',
		'kunci_kehadiran' => 'int'
	];

	protected $fillable = [
		'id_pertemuan',
		'id_jadwal',
		'id_tahun',
		'tgl_pertemuan',
		'kode_kelas',
		'expired_kode',
		'kunci_kehadiran'
	];
}
