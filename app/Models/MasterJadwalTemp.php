<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterJadwalTemp
 * 
 * @property int $id
 * @property string $kode_jadwal
 * @property int $id_dosen
 * @property int $id_dosen2
 * @property int $id_tahun
 * @property string $kode_mata_kuliah
 * @property string $hari
 * @property string $sesi
 * @property string $ruang
 * @property bool|null $kelas
 * @property string|null $rombel
 * @property int $kuota_diambil
 * @property bool $status
 * @property int $tipe_mhs
 * @property string|null $rps
 * @property string|null $kp
 *
 * @package App\Models
 */
class MasterJadwalTemp extends Model
{
	protected $table = 'master_jadwal_temp';
	public $timestamps = false;

	protected $casts = [
		'id_dosen' => 'int',
		'id_dosen2' => 'int',
		'id_tahun' => 'int',
		'kelas' => 'bool',
		'kuota_diambil' => 'int',
		'status' => 'bool',
		'tipe_mhs' => 'int'
	];

	protected $fillable = [
		'kode_jadwal',
		'id_dosen',
		'id_dosen2',
		'id_tahun',
		'kode_mata_kuliah',
		'hari',
		'sesi',
		'ruang',
		'kelas',
		'rombel',
		'kuota_diambil',
		'status',
		'tipe_mhs',
		'rps',
		'kp'
	];
}
