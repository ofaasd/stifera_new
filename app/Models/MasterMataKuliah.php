<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterMataKuliah
 * 
 * @property int $id
 * @property string $kode_mata_kuliah
 * @property string $nama_mata_kuliah
 * @property string $nama_mata_kuliah_eng
 * @property int $jumlah_sks
 * @property int $semester
 * @property string $tp
 * @property string $kelompok_mata_kuliah
 * @property string $rumpun_mata_kuliah
 * @property int $is_aktif
 * @property int $id_program_studi
 * @property Carbon $update_log
 * @property string $kelompok_mata_kuliah_eng
 * @property string|null $rps
 * @property string|null $kp
 *
 * @package App\Models
 */
class MasterMataKuliah extends Model
{
	protected $table = 'master_mata_kuliah';
	public $timestamps = false;

	protected $casts = [
		'jumlah_sks' => 'int',
		'semester' => 'int',
		'is_aktif' => 'int',
		'id_program_studi' => 'int',
		'update_log' => 'datetime'
	];

	protected $fillable = [
		'kode_mata_kuliah',
		'nama_mata_kuliah',
		'nama_mata_kuliah_eng',
		'jumlah_sks',
		'semester',
		'tp',
		'kelompok_mata_kuliah',
		'rumpun_mata_kuliah',
		'is_aktif',
		'id_program_studi',
		'update_log',
		'kelompok_mata_kuliah_eng',
		'rps',
		'kp'
	];
}
