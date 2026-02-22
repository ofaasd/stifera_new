<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiRiwayatPendidikan
 * 
 * @property int $id
 * @property int $id_pegawai
 * @property string $jenjang
 * @property string $universitas
 * @property string $jurusan
 * @property string|null $tempat
 * @property string $no_ijazah
 * @property Carbon $tanggal_ijazah
 * @property Carbon $tahun
 * @property string|null $ijazah
 * @property string $jenjang_profesi
 *
 * @package App\Models
 */
class PegawaiRiwayatPendidikan extends Model
{
	protected $table = 'pegawai_riwayat_pendidikan';
	public $timestamps = false;

	protected $casts = [
		'id_pegawai' => 'int',
		'tanggal_ijazah' => 'datetime',
		'tahun' => 'datetime'
	];

	protected $fillable = [
		'id_pegawai',
		'jenjang',
		'universitas',
		'jurusan',
		'tempat',
		'no_ijazah',
		'tanggal_ijazah',
		'tahun',
		'ijazah',
		'jenjang_profesi'
	];
}
