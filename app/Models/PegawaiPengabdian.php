<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiPengabdian
 * 
 * @property int $id
 * @property int $id_pegawai
 * @property string $nama_kegiatan
 * @property Carbon $tahun
 * @property string $tempat
 * @property string $link_url
 * @property string $bukti
 * @property int $ketua
 * @property string $proposal
 *
 * @package App\Models
 */
class PegawaiPengabdian extends Model
{
	protected $table = 'pegawai_pengabdian';
	public $timestamps = false;

	protected $casts = [
		'id_pegawai' => 'int',
		'tahun' => 'datetime',
		'ketua' => 'int'
	];

	protected $fillable = [
		'id_pegawai',
		'nama_kegiatan',
		'tahun',
		'tempat',
		'link_url',
		'bukti',
		'ketua',
		'proposal'
	];
}
