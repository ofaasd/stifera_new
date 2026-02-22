<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SuratIzinDosen
 * 
 * @property int $id
 * @property int $id_dosen
 * @property Carbon $tgl_surat
 * @property int $jabatan_pengirim
 * @property string $perihal
 * @property string $keterangan
 * @property string $file_surat
 * @property int $dilihat
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class SuratIzinDosen extends Model
{
	protected $table = 'surat_izin_dosen';

	protected $casts = [
		'id_dosen' => 'int',
		'tgl_surat' => 'datetime',
		'jabatan_pengirim' => 'int',
		'dilihat' => 'int'
	];

	protected $fillable = [
		'id_dosen',
		'tgl_surat',
		'jabatan_pengirim',
		'perihal',
		'keterangan',
		'file_surat',
		'dilihat'
	];
}
