<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Menu
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
class Menu extends Model
{
	protected $table = 'menu';
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

	// Relasi untuk mengambil anak (submenu)
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent', 'id')->where('aktif', 1)->orderBy('urut', 'asc');
    }

    // Relasi untuk mengambil induk
    public function parentMenu()
    {
        return $this->belongsTo(Menu::class, 'parent', 'id');
    }
}
