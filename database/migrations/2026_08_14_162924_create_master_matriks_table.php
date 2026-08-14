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
        Schema::create('master_matriks', function (Blueprint $table) {
            $table->id();
            $table->integer('id_matakuliah')->index();
            $table->unsignedBigInteger('id_cpl')->index(); // the id in master_cpl is unsigned big integer or integer. In previous migration, it's `$table->id('id_cpl')`, which evaluates to unsignedBigInteger.
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
        Schema::dropIfExists('master_matriks');
    }
};
