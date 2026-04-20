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
        if (!Schema::hasTable('pegawai_anggota_penelitian')) {
            Schema::create('pegawai_anggota_penelitian', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_penelitian');
                $table->unsignedBigInteger('id_anggota');
                $table->tinyInteger('jenis_anggota')->comment('1=Pegawai, 2=Mahasiswa');
                
                $table->foreign('id_penelitian')->references('id')->on('pegawai_penelitian')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_anggota_penelitian');
    }
};
