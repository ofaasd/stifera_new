<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKr
 * 
 * @property int $id
 * @property int $id_krs
 * @property int $id_jadwal
 * @property int $id_tahun
 * @property string $nim
 * @property string $mata_kuliah
 * @property int $sks
 * @property string $hari
 * @property string $sesi
 * @property string $ruang
 * @property string|null $kelas
 * @property string $id_dosen
 * @property int $is_publish
 * @property Carbon $log_date
 *
 * @package App\Models
 */
class MasterKr extends Model
{
	protected $table = 'master_krs';
	public $timestamps = false;

	protected $casts = [
		'id_krs' => 'int',
		'id_jadwal' => 'int',
		'id_tahun' => 'int',
		'sks' => 'int',
		'is_publish' => 'int',
		'log_date' => 'datetime'
	];

	protected $fillable = [
		'id_krs',
		'id_jadwal',
		'id_tahun',
		'nim',
		'mata_kuliah',
		'sks',
		'hari',
		'sesi',
		'ruang',
		'kelas',
		'id_dosen',
		'is_publish',
		'log_date'
	];
}
