<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YudisiumPendaftaran extends Model
{
    use HasFactory;

    protected $table = 'yudisium_pendaftaran';
    protected $guarded = [];

    // Relasi ke tabel mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id');
    }

    public function periode()
    {
        return $this->belongsTo(YudisiumPeriode::class, 'id_periode', 'id');
    }

    public function berkas()
    {
        return $this->hasMany(YudisiumBerkas::class, 'id_pendaftaran', 'id');
    }
}
