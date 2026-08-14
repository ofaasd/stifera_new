<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_cpl', function (Blueprint $table) {
            $table->id('id_cpl'); // Primary Key, Auto Increment
            $table->integer('id_prodi')->index();
            $table->integer('id_kurikulum')->nullable()->index();
            $table->enum('kategori_aspek', ['Sikap', 'Pengetahuan', 'Keterampilan Umum', 'Keterampilan Khusus']);
            $table->string('kode_cpl', 10);
            $table->text('deskripsi');
            $table->string('referensi', 100)->nullable();
            $table->double('target_capaian')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_cpl');
    }
};
