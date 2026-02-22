<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PmbOnlinePengumuman
 * 
 * @property int $id
 * @property int $id_gelombang
 * @property string $file
 * @property string $keterangan
 * @property Carbon $tanggal
 *
 * @package App\Models
 */
class PmbOnlinePengumuman extends Model
{
	protected $table = 'pmb_online_pengumuman';
	public $timestamps = false;

	protected $casts = [
		'id_gelombang' => 'int',
		'tanggal' => 'datetime'
	];

	protected $fillable = [
		'id_gelombang',
		'file',
		'keterangan',
		'tanggal'
	];
}
