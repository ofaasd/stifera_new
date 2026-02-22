<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class IzinMeninggalkanPekerjaan
 * 
 * @property int $id
 * @property Carbon $tanggal
 * @property Carbon $tanggal_selesai
 * @property Carbon $waktu_mulai
 * @property Carbon $waktu_selesai
 * @property string $keperluan
 * @property int $izin_ka_jenjang
 * @property int $izin_mgr_sdm
 * @property int $id_dosen
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string|null $lampiran
 * @property int|null $id_kategori
 * @property int $jumlah_hari
 *
 * @package App\Models
 */
class IzinMeninggalkanPekerjaan extends Model
{
	protected $table = 'izin_meninggalkan_pekerjaan';

	protected $casts = [
		'tanggal' => 'datetime',
		'tanggal_selesai' => 'datetime',
		'waktu_mulai' => 'datetime',
		'waktu_selesai' => 'datetime',
		'izin_ka_jenjang' => 'int',
		'izin_mgr_sdm' => 'int',
		'id_dosen' => 'int',
		'id_kategori' => 'int',
		'jumlah_hari' => 'int'
	];

	protected $fillable = [
		'tanggal',
		'tanggal_selesai',
		'waktu_mulai',
		'waktu_selesai',
		'keperluan',
		'izin_ka_jenjang',
		'izin_mgr_sdm',
		'id_dosen',
		'lampiran',
		'id_kategori',
		'jumlah_hari'
	];
}
