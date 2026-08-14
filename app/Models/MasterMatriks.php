<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMatriks extends Model
{
    use HasFactory;

    protected $table = 'master_matriks';

    protected $fillable = [
        'id_matakuliah',
        'id_cpl'
    ];

    public function matakuliah()
    {
        return $this->belongsTo(MasterMataKuliah::class, 'id_matakuliah', 'id');
    }

    public function cpl()
    {
        return $this->belongsTo(MasterCpl::class, 'id_cpl', 'id_cpl');
    }
}
