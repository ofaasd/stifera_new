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
        // 1. Kolom token pengawas pada tbl_jadwal_ujian
        Schema::table('tbl_jadwal_ujian', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_jadwal_ujian', 'token_pengawas_uts')) {
                $table->string('token_pengawas_uts', 64)->nullable()->unique()->after('log');
            }
            if (!Schema::hasColumn('tbl_jadwal_ujian', 'token_pengawas_uas')) {
                $table->string('token_pengawas_uas', 64)->nullable()->unique()->after('token_pengawas_uts');
            }
        });

        // 2. Kolom paraf pengawas pada tbl_tempat_ujian
        Schema::table('tbl_tempat_ujian', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_tempat_ujian', 'paraf_pengawas_uts')) {
                $table->longText('paraf_pengawas_uts')->nullable()->after('log');
            }
            if (!Schema::hasColumn('tbl_tempat_ujian', 'waktu_paraf_uts')) {
                $table->timestamp('waktu_paraf_uts')->nullable()->after('paraf_pengawas_uts');
            }
            if (!Schema::hasColumn('tbl_tempat_ujian', 'paraf_pengawas_uas')) {
                $table->longText('paraf_pengawas_uas')->nullable()->after('waktu_paraf_uts');
            }
            if (!Schema::hasColumn('tbl_tempat_ujian', 'waktu_paraf_uas')) {
                $table->timestamp('waktu_paraf_uas')->nullable()->after('paraf_pengawas_uas');
            }
        });

        // 3. Tabel pengaturan ujian / TTD Kepala BAK
        if (!Schema::hasTable('tbl_setting_ujian')) {
            Schema::create('tbl_setting_ujian', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kabak')->nullable();
                $table->string('nip_kabak')->nullable();
                $table->string('ttd_cap_kabak')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_jadwal_ujian', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_jadwal_ujian', 'token_pengawas_uas')) {
                $table->dropColumn('token_pengawas_uas');
            }
            if (Schema::hasColumn('tbl_jadwal_ujian', 'token_pengawas_uts')) {
                $table->dropColumn('token_pengawas_uts');
            }
        });

        Schema::table('tbl_tempat_ujian', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_tempat_ujian', 'waktu_paraf_uas')) {
                $table->dropColumn('waktu_paraf_uas');
            }
            if (Schema::hasColumn('tbl_tempat_ujian', 'paraf_pengawas_uas')) {
                $table->dropColumn('paraf_pengawas_uas');
            }
            if (Schema::hasColumn('tbl_tempat_ujian', 'waktu_paraf_uts')) {
                $table->dropColumn('waktu_paraf_uts');
            }
            if (Schema::hasColumn('tbl_tempat_ujian', 'paraf_pengawas_uts')) {
                $table->dropColumn('paraf_pengawas_uts');
            }
        });

        Schema::dropIfExists('tbl_setting_ujian');
    }
};
