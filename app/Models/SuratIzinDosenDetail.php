<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SuratIzinDosenDetail
 * 
 * @property int $id
 * @property int $id_surat_izin
 * @property int $id_dosen_tujuan
 * @property int $dibaca
 *
 * @package App\Models
 */
class SuratIzinDosenDetail extends Model
{
	protected $table = 'surat_izin_dosen_detail';
	public $timestamps = false;

	protected $casts = [
		'id_surat_izin' => 'int',
		'id_dosen_tujuan' => 'int',
		'dibaca' => 'int'
	];

	protected $fillable = [
		'id_surat_izin',
		'id_dosen_tujuan',
		'dibaca'
	];
}
