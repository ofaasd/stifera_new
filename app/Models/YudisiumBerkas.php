<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YudisiumBerkas extends Model
{
    use HasFactory;

    protected $table = 'yudisium_berkas';
    protected $guarded = [];

    public function pendaftaran()
    {
        return $this->belongsTo(YudisiumPendaftaran::class, 'id_pendaftaran', 'id');
    }
}
