<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProvKotaNew
 * 
 * @property int $id
 * @property string $prov_id
 * @property string|null $nama_prov
 * @property string $kota_id
 * @property string|null $nama_kota
 * @property bool|null $ln
 * @property string|null $id_negara
 * @property string|null $asal_wil
 * @property string|null $kode_bps
 * @property string|null $kode_dagri
 * @property string|null $kode_keu
 * @property string|null $id_induk_wilayah
 * @property int|null $id_level_wil
 *
 * @package App\Models
 */
class ProvKotaNew extends Model
{
	protected $table = 'prov_kota_new';
	public $timestamps = false;

	protected $casts = [
		'ln' => 'bool',
		'id_level_wil' => 'int'
	];

	protected $fillable = [
		'nama_prov',
		'nama_kota',
		'ln',
		'id_negara',
		'asal_wil',
		'kode_bps',
		'kode_dagri',
		'kode_keu',
		'id_induk_wilayah',
		'id_level_wil'
	];
}
