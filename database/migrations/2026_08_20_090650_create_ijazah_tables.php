<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ijazah_periode', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode');
            $table->date('tanggal_yudisium');
            $table->string('nama_ketua')->nullable();
            $table->string('nip_ketua')->nullable();
            $table->string('nama_puket_1')->nullable();
            $table->string('nip_puket_1')->nullable();
            $table->string('nama_kaprodi_s1')->nullable();
            $table->string('nip_kaprodi_s1')->nullable();
            $table->string('nama_kaprodi_d3')->nullable();
            $table->string('nip_kaprodi_d3')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ijazah_dokumen', function (Blueprint $table) {
            $table->id();
            $table->integer('id_mahasiswa');
            $table->unsignedBigInteger('id_periode');
            $table->string('no_ijazah')->nullable();
            $table->string('no_transkrip')->nullable();
            $table->string('pin_dikti')->nullable();
            $table->enum('kategori_kelulusan', ['Memuaskan', 'Sangat Memuaskan', 'Cumlaude', 'Lulus'])->default('Memuaskan');
            $table->date('tanggal_terbit')->nullable();
            $table->timestamps();

            $table->foreign('id_periode')->references('id')->on('ijazah_periode')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ijazah_dokumen');
        Schema::dropIfExists('ijazah_periode');
    }
};