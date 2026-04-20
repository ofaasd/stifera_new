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
        Schema::table('pegawai_penelitian', function (Blueprint $table) {
            // Drop anggota column (no longer needed as we have pegawai_anggota_penelitian table)
            if (Schema::hasColumn('pegawai_penelitian', 'anggota')) {
                $table->dropColumn('anggota');
            }
            
            // Drop old ketua column and create new id_ketua FK
            if (Schema::hasColumn('pegawai_penelitian', 'ketua')) {
                $table->dropColumn('ketua');
            }
            
            if (!Schema::hasColumn('pegawai_penelitian', 'id_ketua')) {
                $table->integer('id_ketua')->nullable()->after('penyelenggara');
                $table->foreign('id_ketua')->references('id')->on('pegawai')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
