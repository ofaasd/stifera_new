<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PmbKela
 * 
 * @property int $no
 * @property int $id
 * @property string $nama_kelas
 * @property bool $jalur
 * @property int $is_active
 *
 * @package App\Models
 */
class PmbKela extends Model
{
	protected $table = 'pmb_kelas';
	protected $primaryKey = 'no';
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'jalur' => 'bool',
		'is_active' => 'int'
	];

	protected $fillable = [
		'id',
		'nama_kelas',
		'jalur',
		'is_active'
	];
}
