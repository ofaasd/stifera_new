<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterJam
 * 
 * @property int $id
 * @property string $nama_sesi
 * @property Carbon $mulai
 * @property Carbon $selesai
 * @property int $sks
 * @property bool $status
 *
 * @package App\Models
 */
class MasterJam extends Model
{
	protected $table = 'master_jam';
	public $timestamps = false;

	protected $casts = [
		'mulai' => 'datetime',
		'selesai' => 'datetime',
		'sks' => 'int',
		'status' => 'bool'
	];

	protected $fillable = [
		'nama_sesi',
		'mulai',
		'selesai',
		'sks',
		'status'
	];
}
