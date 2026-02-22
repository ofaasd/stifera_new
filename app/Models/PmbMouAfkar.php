<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PmbMouAfkar
 * 
 * @property int $id
 * @property string $id_sekolah
 * @property string $nama_sekolah
 *
 * @package App\Models
 */
class PmbMouAfkar extends Model
{
	protected $table = 'pmb_mou_afkar';
	public $timestamps = false;

	protected $fillable = [
		'id_sekolah',
		'nama_sekolah'
	];
}
