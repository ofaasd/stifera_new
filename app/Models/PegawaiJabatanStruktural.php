<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiJabatanStruktural
 * 
 * @property int $id
 * @property int $id_pegawai
 * @property int $unit_kerja
 * @property int $id_jabatan_struktural
 * @property string $no_sk_struktural
 * @property Carbon $tanggal_sk_struktural
 * @property Carbon $tmt_sk_struktural
 * @property int $status
 * @property string $dokumen
 * @property Carbon $tahun_masuk
 * @property Carbon $tahun_keluar
 * @property int $sekarang
 *
 * @package App\Models
 */
class PegawaiJabatanStruktural extends Model
{
	protected $table = 'pegawai_jabatan_struktural';
	public $timestamps = false;

	protected $casts = [
		'id_pegawai' => 'int',
		'unit_kerja' => 'int',
		'id_jabatan_struktural' => 'int',
		'tanggal_sk_struktural' => 'datetime',
		'tmt_sk_struktural' => 'datetime',
		'status' => 'int',
		'sekarang' => 'int'
	];

	protected $fillable = [
		'id_pegawai',
		'unit_kerja',
		'id_jabatan_struktural',
		'no_sk_struktural',
		'tanggal_sk_struktural',
		'tmt_sk_struktural',
		'status',
		'dokumen',
		'tahun_masuk',
		'tahun_keluar',
		'sekarang'
	];
	public function unitKerja()
	{
		return $this->belongsTo(PegawaiUnitKerja::class, 'unit_kerja', 'id');
	}
	public function jabatanStruktural()
	{
		return $this->belongsTo(JabatanStruktural::class, 'id_jabatan_struktural', 'id');
	}
}
