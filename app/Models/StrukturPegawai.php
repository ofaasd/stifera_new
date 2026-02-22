<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class StrukturPegawai
 * 
 * @property int $id
 * @property string|null $ketua_st
 * @property string|null $pembantu_1
 * @property string|null $pembantu_2
 * @property string|null $pembantu_3
 * @property string|null $prodi_d3
 * @property string|null $prodi_s1
 * @property string|null $sekertaris_prodi_d3
 * @property string|null $sekertaris_prodi_s1
 * @property string|null $ketua_mutu
 * @property string|null $ketua_penelitian
 * @property string|null $sekertaris_penelitian
 * @property string|null $koor_lab
 * @property string|null $koor_sarana
 * @property string $ketua_pmb
 *
 * @package App\Models
 */
class StrukturPegawai extends Model
{
	protected $table = 'struktur_pegawai';
	public $timestamps = false;

	protected $fillable = [
		'ketua_st',
		'pembantu_1',
		'pembantu_2',
		'pembantu_3',
		'prodi_d3',
		'prodi_s1',
		'sekertaris_prodi_d3',
		'sekertaris_prodi_s1',
		'ketua_mutu',
		'ketua_penelitian',
		'sekertaris_penelitian',
		'koor_lab',
		'koor_sarana',
		'ketua_pmb'
	];
}
