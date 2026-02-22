<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterPersentaseNilai
 * 
 * @property int $id
 * @property int $id_jadwal
 * @property string $ntugas
 * @property string $nuts
 * @property string $nuas
 * @property Carbon $datetime
 *
 * @package App\Models
 */
class MasterPersentaseNilai extends Model
{
	protected $table = 'master_persentase_nilai';
	public $timestamps = false;

	protected $casts = [
		'id_jadwal' => 'int',
		'datetime' => 'datetime'
	];

	protected $fillable = [
		'id_jadwal',
		'ntugas',
		'nuts',
		'nuas',
		'datetime'
	];
}
