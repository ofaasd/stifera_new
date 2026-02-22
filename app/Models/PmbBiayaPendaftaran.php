<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PmbBiayaPendaftaran
 * 
 * @property int $id
 * @property int $nopen
 * @property int $norek
 * @property int $an_rekening
 *
 * @package App\Models
 */
class PmbBiayaPendaftaran extends Model
{
	protected $table = 'pmb_biaya_pendaftaran';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'nopen' => 'int',
		'norek' => 'int',
		'an_rekening' => 'int'
	];

	protected $fillable = [
		'id',
		'nopen',
		'norek',
		'an_rekening'
	];
}
