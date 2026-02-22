<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Inventari
 * 
 * @property int $id
 * @property string|null $tgl_masuk
 * @property string|null $no_inventaris
 * @property string|null $nama_barang
 * @property string|null $merek
 * @property string|null $user_baru
 * @property string|null $lokasi
 * @property string|null $harga
 * @property string|null $kondisi
 * @property string|null $tgl_penyerahan
 * @property string|null $ket
 * @property string|null $file
 * @property string|null $file2
 *
 * @package App\Models
 */
class Inventari extends Model
{
	protected $table = 'inventaris';
	public $timestamps = false;

	protected $fillable = [
		'tgl_masuk',
		'no_inventaris',
		'nama_barang',
		'merek',
		'user_baru',
		'lokasi',
		'harga',
		'kondisi',
		'tgl_penyerahan',
		'ket',
		'file',
		'file2'
	];
}
