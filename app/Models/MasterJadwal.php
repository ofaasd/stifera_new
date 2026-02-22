<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterJadwal
 * 
 * @property int $id
 * @property int $id_jadwal
 * @property string $kode_jadwal
 * @property int $id_dosen
 * @property int $id_dosen2
 * @property int $id_tahun
 * @property string $kode_mata_kuliah
 * @property string $hari
 * @property string $sesi
 * @property string $ruang
 * @property string|null $kelas
 * @property int $kuota_diambil
 * @property bool $status
 * @property string $rombel
 * @property int $tipe_mhs
 * @property string|null $rps
 * @property string|null $kp
 *
 * @package App\Models
 */
class MasterJadwal extends Model
{
	protected $table = 'master_jadwal';
	public $timestamps = false;

	protected $casts = [
		'id_jadwal' => 'int',
		'id_dosen' => 'int',
		'id_dosen2' => 'int',
		'id_tahun' => 'int',
		'kuota_diambil' => 'int',
		'status' => 'bool',
		'tipe_mhs' => 'int'
	];

	protected $fillable = [
		'id_jadwal',
		'kode_jadwal',
		'id_dosen',
		'id_dosen2',
		'id_tahun',
		'kode_mata_kuliah',
		'hari',
		'sesi',
		'ruang',
		'kelas',
		'kuota_diambil',
		'status',
		'rombel',
		'tipe_mhs',
		'rps',
		'kp'
	];
}
