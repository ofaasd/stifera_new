<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCpl extends Model
{
    use HasFactory;

    protected $table = 'master_cpl';
    protected $primaryKey = 'id_cpl';

    protected $fillable = [
        'id_prodi',
        'id_kurikulum',
        'kategori_aspek',
        'kode_cpl',
        'deskripsi',
        'referensi',
        'target_capaian',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'target_capaian' => 'float',
    ];

    public function prodi()
    {
        return $this->belongsTo(ProgramStudi::class, 'id_prodi', 'id');
    }
}
