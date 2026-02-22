<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ReferensiTahun
 * 
 * @property int $id
 * @property string $ref_tahun
 *
 * @package App\Models
 */
class ReferensiTahun extends Model
{
	protected $table = 'referensi_tahun';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'ref_tahun'
	];
}
