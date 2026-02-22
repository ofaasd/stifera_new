<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PmbSchool
 * 
 * @property int $id
 * @property int|null $npsn
 * @property string|null $nss
 * @property string|null $jenis
 * @property string|null $nama
 * @property string|null $alamat
 * @property string|null $telepon
 * @property string|null $email
 * @property string|null $status
 * @property string $daerah
 * @property string $propinsi
 * @property int|null $prov_id
 *
 * @package App\Models
 */
class PmbSchool extends Model
{
	protected $table = 'pmb_schools';
	public $timestamps = false;

	protected $casts = [
		'npsn' => 'int',
		'prov_id' => 'int'
	];

	protected $fillable = [
		'npsn',
		'nss',
		'jenis',
		'nama',
		'alamat',
		'telepon',
		'email',
		'status',
		'prov_id'
	];
}
