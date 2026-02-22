<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterTemplateTranskrip
 * 
 * @property int $id
 * @property string $matkul
 * @property string $jenjang
 *
 * @package App\Models
 */
class MasterTemplateTranskrip extends Model
{
	protected $table = 'master_template_transkrip';
	public $timestamps = false;

	protected $fillable = [
		'matkul',
		'jenjang'
	];
}
