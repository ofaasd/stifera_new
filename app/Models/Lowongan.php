<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Lowongan
 * 
 * @property int $id
 * @property string $nama_perusahaan
 * @property string $job_title
 * @property string $job_slug
 * @property string $deskripsi
 * @property Carbon $tanggal_posting
 *
 * @package App\Models
 */
class Lowongan extends Model
{
	protected $table = 'lowongan';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'tanggal_posting' => 'datetime'
	];

	protected $fillable = [
		'nama_perusahaan',
		'job_title',
		'job_slug',
		'deskripsi',
		'tanggal_posting'
	];
}
