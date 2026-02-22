<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ReferensiProfesi
 * 
 * @property int $id_profesi
 * @property string $nama_profesi
 *
 * @package App\Models
 */
class ReferensiProfesi extends Model
{
	protected $table = 'referensi_profesi';
	protected $primaryKey = 'id_profesi';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id_profesi' => 'int'
	];

	protected $fillable = [
		'nama_profesi'
	];
}
