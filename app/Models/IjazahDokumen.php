<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IjazahDokumen extends Model
{
    use HasFactory;

    protected $table = 'ijazah_dokumen';

    protected $fillable = [
        'id_mahasiswa',
        'id_periode',
        'no_ijazah',
        'no_transkrip',
        'pin_dikti',
        'kategori_kelulusan',
        'tanggal_terbit'
    ];

    public function periode()
    {
        return $this->belongsTo(IjazahPeriode::class, 'id_periode', 'id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id');
    }
}
