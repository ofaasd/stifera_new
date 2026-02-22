<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class JabatanStruktural
 * 
 * @property int $id
 * @property string $bagian
 * @property string $jabatan
 *
 * @package App\Models
 */
class JabatanStruktural extends Model
{
	protected $table = 'jabatan_struktural';
	public $timestamps = false;

	protected $fillable = [
		'bagian',
		'jabatan'
	];
}
