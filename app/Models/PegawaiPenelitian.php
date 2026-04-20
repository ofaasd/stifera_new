<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PegawaiPenelitian extends Model
{
	protected $table = 'pegawai_penelitian';
	public $timestamps = false;

	protected $casts = [
		'id_pegawai' => 'int',
		'id_fakultas' => 'int',
		'id_ketua' => 'int',
		'tahun' => 'datetime',
		'dana' => 'int'
	];

	protected $fillable = [
		'id_pegawai',
		'nomor',
		'judul',
		'id_fakultas',
		'jenis_penelitian',
		'tahun',
		'sumber_dana',
		'dana',
		'no_surat',
		'penyelenggara',
		'id_ketua',
		'dokumen',
		'proposal',
		'lap_kemajuan',
		'lap_keuangan',
		'lap_akhir'
	];

	public function pegawai()
	{
		return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id');
	}

	public function ketua()
	{
		return $this->belongsTo(Pegawai::class, 'id_ketua', 'id');
	}

	public function anggota()
	{
		return $this->hasMany(PegawaiAnggotaPenelitian::class, 'id_penelitian', 'id');
	}
}
