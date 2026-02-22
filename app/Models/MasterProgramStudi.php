<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterProgramStudi
 * 
 * @property int $id
 * @property string|null $nama_jurusan
 *
 * @package App\Models
 */
class MasterProgramStudi extends Model
{
	protected $table = 'master_program_studi';
	public $timestamps = false;

	protected $fillable = [
		'nama_jurusan'
	];
}
