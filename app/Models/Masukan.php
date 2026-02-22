<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Masukan
 * 
 * @property int $id
 * @property string $nim
 * @property string $saran
 * @property Carbon $tanggal
 * @property Carbon $tanggal_tanggapan
 * @property string $tindak_lanjut
 * @property string $status
 *
 * @package App\Models
 */
class Masukan extends Model
{
	protected $table = 'masukan';
	public $timestamps = false;

	protected $casts = [
		'tanggal' => 'datetime',
		'tanggal_tanggapan' => 'datetime'
	];

	protected $fillable = [
		'nim',
		'saran',
		'tanggal',
		'tanggal_tanggapan',
		'tindak_lanjut',
		'status'
	];
}
