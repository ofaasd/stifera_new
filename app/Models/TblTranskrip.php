<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblTranskrip
 * 
 * @property int $id
 * @property string $nim
 * @property Carbon $tgl_masuk
 * @property Carbon $tgl_keluar
 * @property string $no_transkrip
 * @property string $no_ijazah
 * @property string $judul_kti
 * @property string $tempat_pkl
 * @property string $judul_tugas_akhir
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class TblTranskrip extends Model
{
	protected $table = 'tbl_transkrip';
	public $timestamps = false;

	protected $casts = [
		'tgl_masuk' => 'datetime',
		'tgl_keluar' => 'datetime'
	];

	protected $fillable = [
		'nim',
		'tgl_masuk',
		'tgl_keluar',
		'no_transkrip',
		'no_ijazah',
		'judul_kti',
		'tempat_pkl',
		'judul_tugas_akhir'
	];
}
