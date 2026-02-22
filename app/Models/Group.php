<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Group
 * 
 * @property int $id
 * @property string $name
 * @property string $description
 *
 * @package App\Models
 */
class Group extends Model
{
	protected $table = 'groups';
	public $timestamps = false;

	protected $fillable = [
		'name',
		'description'
	];
}
