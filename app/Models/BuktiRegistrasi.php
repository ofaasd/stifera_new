<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BuktiRegistrasi
 * 
 * @property int $id
 * @property int|null $id_rekening
 * @property string|null $nopen
 * @property string|null $norek_pengirim
 * @property string|null $an_pengirim
 * @property Carbon|null $tgl_tf
 * @property string|null $bukti
 * @property int|null $is_online
 * @property Carbon|null $created_at
 * @property int|null $user_id
 *
 * @package App\Models
 */
class BuktiRegistrasi extends Model
{
	protected $table = 'bukti_registrasi';
	public $timestamps = false;

	protected $casts = [
		'id_rekening' => 'int',
		'tgl_tf' => 'datetime',
		'is_online' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'id_rekening',
		'nopen',
		'norek_pengirim',
		'an_pengirim',
		'tgl_tf',
		'bukti',
		'is_online',
		'user_id'
	];
}
