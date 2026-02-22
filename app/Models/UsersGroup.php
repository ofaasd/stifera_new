<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UsersGroup
 * 
 * @property int $id
 * @property int $user_id
 * @property int $group_id
 *
 * @package App\Models
 */
class UsersGroup extends Model
{
	protected $table = 'users_groups';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'user_id' => 'int',
		'group_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'group_id'
	];
}
