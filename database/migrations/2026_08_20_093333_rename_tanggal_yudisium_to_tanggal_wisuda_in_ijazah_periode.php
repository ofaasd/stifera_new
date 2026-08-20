<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ijazah_periode', function (Blueprint $table) {
            $table->renameColumn('tanggal_yudisium', 'tanggal_wisuda');
        });
    }

    public function down()
    {
        Schema::table('ijazah_periode', function (Blueprint $table) {
            $table->renameColumn('tanggal_wisuda', 'tanggal_yudisium');
        });
    }
};
