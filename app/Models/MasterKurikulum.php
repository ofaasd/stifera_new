<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterKurikulum
 * 
 * @property int $id
 * @property string $kode_kurikulum
 * @property string $progdi
 * @property string $thn_ajar
 * @property string $angkatan
 * @property int $status
 * @property Carbon $log_update
 *
 * @package App\Models
 */
class MasterKurikulum extends Model
{
	protected $table = 'master_kurikulum';
	public $timestamps = false;

	protected $casts = [
		'status' => 'int',
		'log_update' => 'datetime'
	];

	protected $fillable = [
		'kode_kurikulum',
		'progdi',
		'thn_ajar',
		'angkatan',
		'status',
		'log_update'
	];
}
