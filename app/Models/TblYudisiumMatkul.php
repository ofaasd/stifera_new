<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblYudisiumMatkul
 * 
 * @property int $id
 * @property string $kode_matkul
 * @property Carbon $tahun_yudisium
 * @property int $id_jurusan
 * @property int $kelas
 *
 * @package App\Models
 */
class TblYudisiumMatkul extends Model
{
	protected $table = 'tbl_yudisium_matkul';
	public $timestamps = false;

	protected $casts = [
		'tahun_yudisium' => 'datetime',
		'id_jurusan' => 'int',
		'kelas' => 'int'
	];

	protected $fillable = [
		'kode_matkul',
		'tahun_yudisium',
		'id_jurusan',
		'kelas'
	];
}
