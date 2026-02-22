<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterPost
 * 
 * @property int $id
 * @property string $gambar
 * @property string $judul
 * @property string $isi
 * @property Carbon $tgl_awal
 * @property Carbon $tgl_akhir
 * @property int $kategori
 *
 * @package App\Models
 */
class MasterPost extends Model
{
	protected $table = 'master_post';
	public $timestamps = false;

	protected $casts = [
		'tgl_awal' => 'datetime',
		'tgl_akhir' => 'datetime',
		'kategori' => 'int'
	];

	protected $fillable = [
		'gambar',
		'judul',
		'isi',
		'tgl_awal',
		'tgl_akhir',
		'kategori'
	];
}
