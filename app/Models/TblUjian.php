<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblUjian
 * 
 * @property int $id
 * @property int $id_jadwal
 * @property Carbon|null $tgl_ujian
 * @property int|null $jenis_ujian
 * @property int $is_publish
 *
 * @package App\Models
 */
class TblUjian extends Model
{
	protected $table = 'tbl_ujian';
	public $timestamps = false;

	protected $casts = [
		'id_jadwal' => 'int',
		'tgl_ujian' => 'datetime',
		'jenis_ujian' => 'int',
		'is_publish' => 'int'
	];

	protected $fillable = [
		'id_jadwal',
		'tgl_ujian',
		'jenis_ujian',
		'is_publish'
	];
}
