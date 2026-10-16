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
        if (Schema::hasTable('pegawai_biodata') && !Schema::hasColumn('pegawai_biodata', 'paraf')) {
            Schema::table('pegawai_biodata', function (Blueprint $table) {
                $table->longText('paraf')->nullable()->after('foto');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pegawai_biodata') && Schema::hasColumn('pegawai_biodata', 'paraf')) {
            Schema::table('pegawai_biodata', function (Blueprint $table) {
                $table->dropColumn('paraf');
            });
        }
    }
};
