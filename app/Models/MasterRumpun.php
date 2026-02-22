<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterRumpun
 * 
 * @property int $id
 * @property string $nama_rumpun
 * @property bool $status
 *
 * @package App\Models
 */
class MasterRumpun extends Model
{
	protected $table = 'master_rumpun';
	public $timestamps = false;

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'nama_rumpun',
		'status'
	];
}
