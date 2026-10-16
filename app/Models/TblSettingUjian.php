<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblSettingUjian extends Model
{
    protected $table = 'tbl_setting_ujian';

    protected $fillable = [
        'nama_kabak',
        'nip_kabak',
        'ttd_cap_kabak',
    ];
}
