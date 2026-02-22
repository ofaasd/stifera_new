<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Dokuman
 * 
 * @property int $id
 * @property string $nama_file
 * @property string $deskripsi
 * @property string $file
 *
 * @package App\Models
 */
class Dokuman extends Model
{
	protected $table = 'dokumen';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int'
	];

	protected $fillable = [
		'nama_file',
		'deskripsi',
		'file'
	];
}
