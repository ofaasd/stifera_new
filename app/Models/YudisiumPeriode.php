<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YudisiumPeriode extends Model
{
    use HasFactory;

    protected $table = 'yudisium_periode';
    protected $guarded = [];

    public function pendaftaran()
    {
        return $this->hasMany(YudisiumPendaftaran::class, 'id_periode', 'id');
    }
}
