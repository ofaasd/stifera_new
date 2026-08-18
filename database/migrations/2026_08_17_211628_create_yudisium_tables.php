<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('yudisium_periode', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode');
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('yudisium_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_periode');
            $table->unsignedBigInteger('id_mahasiswa');
            $table->enum('status_pengajuan', ['menunggu', 'revisi', 'valid', 'lulus_yudisium'])->default('menunggu');
            $table->boolean('is_hardcopy_pkk')->default(false);
            $table->boolean('is_hardcopy_skripsi')->default(false);
            $table->string('no_sk_yudisium')->nullable();
            $table->date('tgl_yudisium')->nullable();
            $table->timestamps();
        });

        Schema::create('yudisium_berkas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pendaftaran');
            $table->string('jenis_berkas'); // laporan_pkk, agenda_pkk, skripsi, dll
            $table->string('file_path');
            $table->enum('status_berkas', ['menunggu', 'valid', 'tolak'])->default('menunggu');
            $table->text('catatan_revisi')->nullable();
            $table->timestamps();

            $table->foreign('id_pendaftaran')->references('id')->on('yudisium_pendaftaran')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yudisium_berkas');
        Schema::dropIfExists('yudisium_pendaftaran');
        Schema::dropIfExists('yudisium_periode');
    }
};
