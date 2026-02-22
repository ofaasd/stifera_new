<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiBkd
 * 
 * @property int $id
 * @property int $id_pegawai
 * @property string|null $periode_bkd
 * @property string|null $status
 * @property string|null $jabatan_fungsional
 * @property string|null $assesor1
 * @property int|null $status_validasi1
 * @property string|null $assesor2
 * @property int|null $status_validasi2
 * @property string|null $lampiran
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class PegawaiBkd extends Model
{
	protected $table = 'pegawai_bkd';

	protected $casts = [
		'id_pegawai' => 'int',
		'status_validasi1' => 'int',
		'status_validasi2' => 'int'
	];

	protected $fillable = [
		'id_pegawai',
		'periode_bkd',
		'status',
		'jabatan_fungsional',
		'assesor1',
		'status_validasi1',
		'assesor2',
		'status_validasi2',
		'lampiran'
	];
}
