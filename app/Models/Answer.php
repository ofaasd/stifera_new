<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Answer
 * 
 * @property int $id
 * @property int $survey_id
 * @property int $user_id
 * @property string $answer
 * @property int $question_id
 * @property Carbon $date_created
 *
 * @package App\Models
 */
class Answer extends Model
{
	protected $table = 'answers';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'survey_id' => 'int',
		'user_id' => 'int',
		'question_id' => 'int',
		'date_created' => 'datetime'
	];

	protected $fillable = [
		'id',
		'survey_id',
		'user_id',
		'answer',
		'question_id',
		'date_created'
	];
}
