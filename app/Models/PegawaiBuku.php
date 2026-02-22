<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PegawaiBuku
 * 
 * @property int $id
 * @property int $id_pegawai
 * @property string $judul_buku
 * @property string $penulis
 * @property string $isbn
 * @property int $tahun
 * @property string $link_dokumen
 *
 * @package App\Models
 */
class PegawaiBuku extends Model
{
	protected $table = 'pegawai_buku';
	public $timestamps = false;

	protected $casts = [
		'id_pegawai' => 'int',
		'tahun' => 'int'
	];

	protected $fillable = [
		'id_pegawai',
		'judul_buku',
		'penulis',
		'isbn',
		'tahun',
		'link_dokumen'
	];
}
