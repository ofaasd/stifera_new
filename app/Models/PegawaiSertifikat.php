<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiSertifikat
 * 
 * @property int $id
 * @property int $id_pegawai
 * @property string $nama_sertifikat
 * @property string|null $file
 * @property Carbon $tahun_ajaran
 * @property string $nama_kegiatan
 * @property string $penyelenggara
 * @property Carbon $tanggal_kegiatan
 * @property string $peran_sebagai
 *
 * @package App\Models
 */
class PegawaiSertifikat extends Model
{
	protected $table = 'pegawai_sertifikat';
	public $timestamps = false;

	protected $casts = [
		'id_pegawai' => 'int',
		'tahun_ajaran' => 'datetime',
		'tanggal_kegiatan' => 'datetime'
	];

	protected $fillable = [
		'id_pegawai',
		'nama_sertifikat',
		'file',
		'tahun_ajaran',
		'nama_kegiatan',
		'penyelenggara',
		'tanggal_kegiatan',
		'peran_sebagai'
	];
}
