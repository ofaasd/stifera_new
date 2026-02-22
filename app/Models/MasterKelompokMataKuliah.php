<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKelompokMataKuliah
 * 
 * @property int $id
 * @property string $nama_kelompok
 * @property string $nama_kelompok_eng
 * @property string $kode
 *
 * @package App\Models
 */
class MasterKelompokMataKuliah extends Model
{
	protected $table = 'master_kelompok_mata_kuliah';
	public $timestamps = false;

	protected $fillable = [
		'nama_kelompok',
		'nama_kelompok_eng',
		'kode'
	];
}
