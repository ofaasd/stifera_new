<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        DB::statement("ALTER TABLE yudisium_pendaftaran MODIFY COLUMN status_pengajuan ENUM('draft', 'menunggu', 'revisi', 'valid', 'lulus_yudisium') NOT NULL DEFAULT 'draft'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE yudisium_pendaftaran MODIFY COLUMN status_pengajuan ENUM('menunggu', 'revisi', 'valid', 'lulus_yudisium') NOT NULL DEFAULT 'menunggu'");
    }
};
