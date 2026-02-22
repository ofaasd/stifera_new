<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MenuTree
 * 
 * @property int $id
 * @property string|null $nama_menu
 * @property int|null $parent
 * @property string|null $url
 * @property string|null $icon
 * @property int|null $urut
 * @property bool|null $aktif
 * @property string|null $stat
 *
 * @package App\Models
 */
class MenuTree extends Model
{
	protected $table = 'menu_tree';
	public $timestamps = false;

	protected $casts = [
		'parent' => 'int',
		'urut' => 'int',
		'aktif' => 'bool'
	];

	protected $fillable = [
		'nama_menu',
		'parent',
		'url',
		'icon',
		'urut',
		'aktif',
		'stat'
	];
}
