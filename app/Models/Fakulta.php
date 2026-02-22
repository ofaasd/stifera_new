<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Fakulta
 * 
 * @property int $id
 * @property string|null $kode
 * @property string|null $nama_fakultas
 * @property Carbon $tgl_berdiri
 * @property bool|null $is_aktif
 *
 * @package App\Models
 */
class Fakulta extends Model
{
	protected $table = 'fakultas';
	public $timestamps = false;

	protected $casts = [
		'tgl_berdiri' => 'datetime',
		'is_aktif' => 'bool'
	];

	protected $fillable = [
		'kode',
		'nama_fakultas',
		'tgl_berdiri',
		'is_aktif'
	];
}
