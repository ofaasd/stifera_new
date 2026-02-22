<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LoginAttempt
 * 
 * @property int $id
 * @property string $ip_address
 * @property string $login
 * @property int|null $time
 *
 * @package App\Models
 */
class LoginAttempt extends Model
{
	protected $table = 'login_attempts';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'time' => 'int'
	];

	protected $fillable = [
		'ip_address',
		'login',
		'time'
	];
}
