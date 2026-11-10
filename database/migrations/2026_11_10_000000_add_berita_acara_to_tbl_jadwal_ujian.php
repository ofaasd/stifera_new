<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('tbl_jadwal_ujian')) {
            Schema::table('tbl_jadwal_ujian', function (Blueprint $table) {
                if (!Schema::hasColumn('tbl_jadwal_ujian', 'catatan_pengawas_uts')) {
                    $table->text('catatan_pengawas_uts')->nullable()->after('token_pengawas_uas');
                }
                if (!Schema::hasColumn('tbl_jadwal_ujian', 'catatan_pengawas_uas')) {
                    $table->text('catatan_pengawas_uas')->nullable()->after('catatan_pengawas_uts');
                }
                if (!Schema::hasColumn('tbl_jadwal_ujian', 'nama_pengawas_uts')) {
                    $table->string('nama_pengawas_uts', 150)->nullable()->after('catatan_pengawas_uas');
                }
                if (!Schema::hasColumn('tbl_jadwal_ujian', 'nama_pengawas_uas')) {
                    $table->string('nama_pengawas_uas', 150)->nullable()->after('nama_pengawas_uts');
                }
                if (!Schema::hasColumn('tbl_jadwal_ujian', 'master_paraf_uts')) {
                    $table->longText('master_paraf_uts')->nullable()->after('nama_pengawas_uas');
                }
                if (!Schema::hasColumn('tbl_jadwal_ujian', 'master_paraf_uas')) {
                    $table->longText('master_paraf_uas')->nullable()->after('master_paraf_uts');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tbl_jadwal_ujian')) {
            Schema::table('tbl_jadwal_ujian', function (Blueprint $table) {
                $cols = [
                    'catatan_pengawas_uts',
                    'catatan_pengawas_uas',
                    'nama_pengawas_uts',
                    'nama_pengawas_uas',
                    'master_paraf_uts',
                    'master_paraf_uas',
                ];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('tbl_jadwal_ujian', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};

