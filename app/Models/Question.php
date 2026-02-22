<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Question
 * 
 * @property int $id
 * @property string $question
 * @property string $frm_option
 * @property string $type
 * @property int $order_by
 * @property int $survey_id
 * @property Carbon $date_created
 *
 * @package App\Models
 */
class Question extends Model
{
	protected $table = 'questions';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'id' => 'int',
		'order_by' => 'int',
		'survey_id' => 'int',
		'date_created' => 'datetime'
	];

	protected $fillable = [
		'id',
		'question',
		'frm_option',
		'type',
		'order_by',
		'survey_id',
		'date_created'
	];
}
