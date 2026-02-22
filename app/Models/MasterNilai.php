<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterNilai
 * 
 * @property int $id
 * @property int $id_jadwal
 * @property int $id_tahun
 * @property string $nim
 * @property string|null $ntugas
 * @property string|null $nuts
 * @property string|null $nuas
 * @property string|null $nakhir
 * @property string|null $nhuruf
 * @property string $ndosen
 * @property int $is_krs
 * @property int $publish_tugas
 * @property int $publish_uts
 * @property int $publish_uas
 * @property int $validasi_tugas
 * @property int $validasi_uts
 * @property int $validasi_uas
 * @property Carbon $log_date
 *
 * @package App\Models
 */
class MasterNilai extends Model
{
	protected $table = 'master_nilai';
	public $timestamps = false;

	protected $casts = [
		'id_jadwal' => 'int',
		'id_tahun' => 'int',
		'is_krs' => 'int',
		'publish_tugas' => 'int',
		'publish_uts' => 'int',
		'publish_uas' => 'int',
		'validasi_tugas' => 'int',
		'validasi_uts' => 'int',
		'validasi_uas' => 'int',
		'log_date' => 'datetime'
	];

	protected $fillable = [
		'id_jadwal',
		'id_tahun',
		'nim',
		'ntugas',
		'nuts',
		'nuas',
		'nakhir',
		'nhuruf',
		'ndosen',
		'is_krs',
		'publish_tugas',
		'publish_uts',
		'publish_uas',
		'validasi_tugas',
		'validasi_uts',
		'validasi_uas',
		'log_date'
	];
}
