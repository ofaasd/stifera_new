<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PmbPernyataan
 * 
 * @property int $id
 * @property int $nopen
 * @property int $spi
 * @property int $tahap1
 * @property int $tahap2
 * @property int $tahap3
 *
 * @package App\Models
 */
class PmbPernyataan extends Model
{
	protected $table = 'pmb_pernyataan';
	public $timestamps = false;

	protected $casts = [
		'nopen' => 'int',
		'spi' => 'int',
		'tahap1' => 'int',
		'tahap2' => 'int',
		'tahap3' => 'int'
	];

	protected $fillable = [
		'nopen',
		'spi',
		'tahap1',
		'tahap2',
		'tahap3'
	];
}
