<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MasterRekening
 * 
 * @property int $id
 * @property string $nama_bank
 * @property string $norek
 * @property string $atas_nama
 *
 * @package App\Models
 */
class MasterRekening extends Model
{
	protected $table = 'master_rekening';
	public $timestamps = false;

	protected $fillable = [
		'nama_bank',
		'norek',
		'atas_nama'
	];
}
