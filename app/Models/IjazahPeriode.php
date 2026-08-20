<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IjazahPeriode extends Model
{
    use HasFactory;

    protected $table = 'ijazah_periode';

    protected $fillable = [
        'nama_periode',
        'tanggal_wisuda',
        'nama_ketua',
        'nip_ketua',
        'nama_puket_1',
        'nip_puket_1',
        'nama_kaprodi_s1',
        'nip_kaprodi_s1',
        'nama_kaprodi_d3',
        'nip_kaprodi_d3',
        'is_active'
    ];

    public function dokumen()
    {
        return $this->hasMany(IjazahDokumen::class, 'id_periode', 'id');
    }
}
