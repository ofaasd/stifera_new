<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TblSoalKuesioner
 * 
 * @property int $id
 * @property int $id_ta
 * @property string $no_soal
 * @property string $soal
 * @property int $category
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models
 */
class TblSoalKuesioner extends Model
{
	public const CATEGORY_OPTIONS = [
		1 => 'Materi Perkuliahan',
		2 => 'Kompetensi Professional',
		3 => 'Interaksi Dosen dan Mahasiswa',
		4 => 'Kepuasan Sarana Prasarana',
		5 => 'Kepuasan Pelayanan Tenaga Kependidikan',
		6 => 'Kepuasan Pelayanan Pengelola (STIFERA)',
	];

	protected $table = 'tbl_soal_kuesioner';

	protected $casts = [
		'id_ta' => 'int',
		'category' => 'int'
	];

	protected $fillable = [
		'id_ta',
		'no_soal',
		'soal',
		'category'
	];

	public static function categoryOptions(): array
	{
		return self::CATEGORY_OPTIONS;
	}

	public static function categoryLabel(?int $category): string
	{
		return self::CATEGORY_OPTIONS[(int) $category] ?? '-';
	}
}
