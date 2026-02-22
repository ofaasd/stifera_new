<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SurveySet
 * 
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $user_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property Carbon $date_created
 *
 * @package App\Models
 */
class SurveySet extends Model
{
	protected $table = 'survey_set';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'user_id' => 'int',
		'start_date' => 'datetime',
		'end_date' => 'datetime',
		'date_created' => 'datetime'
	];

	protected $fillable = [
		'id',
		'title',
		'description',
		'user_id',
		'start_date',
		'end_date',
		'date_created'
	];
}
