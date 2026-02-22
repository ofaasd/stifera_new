<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MenuLevelSimpeg
 * 
 * @property int $id
 * @property int|null $level_id
 * @property int|null $menu_id
 *
 * @package App\Models
 */
class MenuLevelSimpeg extends Model
{
	protected $table = 'menu_level_simpeg';
	public $timestamps = false;

	protected $casts = [
		'level_id' => 'int',
		'menu_id' => 'int'
	];

	protected $fillable = [
		'level_id',
		'menu_id'
	];
}
