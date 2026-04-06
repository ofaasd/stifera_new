<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class TblSoalPmb
 * 
 * @property int $id
 * @property string $soal
 * @property int $is_aktif
 * @property int $by_excel
 * @property Carbon $created_by
 *
 * @package App\Models
 */
class TblSoalPmb extends Model
{
	protected $table = 'tbl_soal_pmb';
	public $timestamps = false;

	protected $casts = [
		'is_aktif' => 'int',
		'by_excel' => 'int',
		'created_by' => 'datetime'
	];

	protected $fillable = [
		'soal',
		'is_aktif',
		'by_excel',
		'created_by'
	];

	public function pilihanSoal(): HasMany
	{
		return $this->hasMany(TblPilihanSoal::class, 'id_soal')->orderBy('id');
	}

	public function kunciJawaban(): HasOne
	{
		return $this->hasOne(TblKunci::class, 'id_soal');
	}
}
