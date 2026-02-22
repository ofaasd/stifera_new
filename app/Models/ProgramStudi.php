<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProgramStudi
 * 
 * @property int $id
 * @property string|null $kode
 * @property string $kodenim
 * @property string|null $jenjang
 * @property string|null $nama_jurusan
 * @property string $nama_ijazah
 * @property string $nama_ijazah_eng
 * @property int|null $fakultas
 * @property bool|null $off
 *
 * @package App\Models
 */
class ProgramStudi extends Model
{
	protected $table = 'program_studi';
	public $timestamps = false;

	protected $casts = [
		'fakultas' => 'int',
		'off' => 'bool'
	];

	protected $fillable = [
		'kode',
		'kodenim',
		'jenjang',
		'nama_jurusan',
		'nama_ijazah',
		'nama_ijazah_eng',
		'fakultas',
		'off'
	];
}
