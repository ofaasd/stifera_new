<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PegawaiAnggotaPenelitian extends Model
{
	protected $table = 'pegawai_anggota_penelitian';
	public $timestamps = false;

	protected $casts = [
		'jenis_anggota' => 'int',
		'id_penelitian' => 'int',
		'id_anggota' => 'int'
	];

	protected $fillable = [
		'jenis_anggota',
		'id_anggota',
		'id_penelitian'
	];

	public function penelitian()
	{
		return $this->belongsTo(PegawaiPenelitian::class, 'id_penelitian', 'id');
	}

	public function pegawai()
	{
		return $this->belongsTo(Pegawai::class, 'id_anggota', 'id');
	}

	public function mahasiswa()
	{
		return $this->belongsTo(Mahasiswa::class, 'id_anggota', 'id');
	}
}
