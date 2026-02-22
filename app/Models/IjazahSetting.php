<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class IjazahSetting
 * 
 * @property int $id
 * @property string $status
 * @property int $ketua_prodi
 * @property int $ketua_sekolah
 * @property Carbon $tanggal_ijazah
 * @property Carbon $tanggal_pembuatan
 * @property string $izin
 *
 * @package App\Models
 */
class IjazahSetting extends Model
{
	protected $table = 'ijazah_setting';
	public $timestamps = false;

	protected $casts = [
		'ketua_prodi' => 'int',
		'ketua_sekolah' => 'int',
		'tanggal_ijazah' => 'datetime',
		'tanggal_pembuatan' => 'datetime'
	];

	protected $fillable = [
		'status',
		'ketua_prodi',
		'ketua_sekolah',
		'tanggal_ijazah',
		'tanggal_pembuatan',
		'izin'
	];
}
