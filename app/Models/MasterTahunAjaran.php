<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterTahunAjaran
 * 
 * @property int $id
 * @property int $id_tahun
 * @property string $awal
 * @property string $akhir
 * @property int $jenis
 * @property string|null $is_delete
 * @property int $is_aktif
 * @property int $status
 * @property int $tipe_mhs
 * @property int $kuesioner
 *
 * @package App\Models
 */
class MasterTahunAjaran extends Model
{
	protected $table = 'master_tahun_ajaran';
	public $timestamps = false;

	protected $casts = [
		'id_tahun' => 'int',
		'jenis' => 'int',
		'is_aktif' => 'int',
		'status' => 'int',
		'tipe_mhs' => 'int',
		'kuesioner' => 'int'
	];

	protected $fillable = [
		'id_tahun',
		'awal',
		'akhir',
		'jenis',
		'is_delete',
		'is_aktif',
		'status',
		'tipe_mhs',
		'kuesioner'
	];
}
