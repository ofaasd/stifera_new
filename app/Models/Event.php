<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Event
 * 
 * @property int $id
 * @property string $nama_event
 * @property string $event_title
 * @property string $event_slug
 * @property string $deskripsi
 * @property Carbon $tanggal_posting
 *
 * @package App\Models
 */
class Event extends Model
{
	protected $table = 'event';
	public $timestamps = false;

	protected $casts = [
		'tanggal_posting' => 'datetime'
	];

	protected $fillable = [
		'nama_event',
		'event_title',
		'event_slug',
		'deskripsi',
		'tanggal_posting'
	];
}
