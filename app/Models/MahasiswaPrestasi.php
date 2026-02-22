<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MahasiswaPrestasi
 * 
 * @property int $id
 * @property string $nim
 * @property int $jenis
 * @property string|null $peringkat
 * @property string $tingkat
 * @property string $penyelenggara
 * @property string $file
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class MahasiswaPrestasi extends Model
{
	protected $table = 'mahasiswa_prestasi';

	protected $casts = [
		'jenis' => 'int'
	];

	protected $fillable = [
		'nim',
		'jenis',
		'peringkat',
		'tingkat',
		'penyelenggara',
		'file'
	];
}
