<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class JabatanNew
 * 
 * @property int $id
 * @property string $nama_jabatan
 * @property string $nama_judul
 * @property string $id_pegawai
 *
 * @package App\Models
 */
class JabatanNew extends Model
{
	protected $table = 'jabatan_new';
	public $timestamps = false;

	protected $fillable = [
		'nama_jabatan',
		'nama_judul',
		'id_pegawai'
	];
}
