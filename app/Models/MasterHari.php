<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterHari
 * 
 * @property int $id
 * @property string $nama_hari
 *
 * @package App\Models
 */
class MasterHari extends Model
{
	protected $table = 'master_hari';
	public $timestamps = false;

	protected $fillable = [
		'nama_hari'
	];
}
