<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblYudisiumNilai
 * 
 * @property int $id
 * @property string $nim
 * @property string $kode_matkul
 * @property float|null $nilai
 * @property int $id_jurusan
 * @property Carbon $tahun_yudisium
 * @property string $nilai_huruf
 * @property string $nama
 * @property int $kelas
 *
 * @package App\Models
 */
class TblYudisiumNilai extends Model
{
	protected $table = 'tbl_yudisium_nilai';
	public $timestamps = false;

	protected $casts = [
		'nilai' => 'float',
		'id_jurusan' => 'int',
		'tahun_yudisium' => 'datetime',
		'kelas' => 'int'
	];

	protected $fillable = [
		'nim',
		'kode_matkul',
		'nilai',
		'id_jurusan',
		'tahun_yudisium',
		'nilai_huruf',
		'nama',
		'kelas'
	];
}
