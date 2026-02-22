<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PmbRiwayatPembayaran
 * 
 * @property int $id
 * @property int $nopen
 * @property int $tagihan_sebelum
 * @property int $dibayar
 * @property int $tagihan_sekarang
 * @property Carbon $tgl_pembayaran
 * @property string|null $norek_pengirim
 * @property string|null $an_pengirim
 * @property string|null $media_pembayaran
 * @property string|null $keterangan
 * @property int $dicatat
 * @property Carbon $log
 *
 * @package App\Models
 */
class PmbRiwayatPembayaran extends Model
{
	protected $table = 'pmb_riwayat_pembayaran';
	public $timestamps = false;

	protected $casts = [
		'nopen' => 'int',
		'tagihan_sebelum' => 'int',
		'dibayar' => 'int',
		'tagihan_sekarang' => 'int',
		'tgl_pembayaran' => 'datetime',
		'dicatat' => 'int',
		'log' => 'datetime'
	];

	protected $fillable = [
		'nopen',
		'tagihan_sebelum',
		'dibayar',
		'tagihan_sekarang',
		'tgl_pembayaran',
		'norek_pengirim',
		'an_pengirim',
		'media_pembayaran',
		'keterangan',
		'dicatat',
		'log'
	];
}
