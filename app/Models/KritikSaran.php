<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class KritikSaran
 * 
 * @property int $id_kritiksaran
 * @property int $id_user
 * @property string $kritik
 * @property string $saran
 * @property Carbon $tanggal_posting
 *
 * @package App\Models
 */
class KritikSaran extends Model
{
	protected $table = 'kritik_saran';
	protected $primaryKey = 'id_kritiksaran';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_kritiksaran' => 'int',
		'id_user' => 'int',
		'tanggal_posting' => 'datetime'
	];

	protected $fillable = [
		'id_user',
		'kritik',
		'saran',
		'tanggal_posting'
	];
}
