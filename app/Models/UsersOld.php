<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UsersOld
 * 
 * @property int $id
 * @property int|null $userid
 * @property int|null $role
 * @property int $simpeg_role
 * @property Carbon|null $login_date
 * @property Carbon|null $last_login
 * @property int|null $app_id
 * @property bool|null $active
 * @property string|null $login_log_ip
 * @property Carbon|null $login_log_date
 * @property string|null $last_login_ip
 * @property Carbon|null $last_login_date
 *
 * @package App\Models
 */
class UsersOld extends Model
{
	protected $table = 'users_old';
	public $timestamps = false;

	protected $casts = [
		'userid' => 'int',
		'role' => 'int',
		'simpeg_role' => 'int',
		'login_date' => 'datetime',
		'last_login' => 'datetime',
		'app_id' => 'int',
		'active' => 'bool',
		'login_log_date' => 'datetime',
		'last_login_date' => 'datetime'
	];

	protected $fillable = [
		'userid',
		'role',
		'simpeg_role',
		'login_date',
		'last_login',
		'app_id',
		'active',
		'login_log_ip',
		'login_log_date',
		'last_login_ip',
		'last_login_date'
	];
}
