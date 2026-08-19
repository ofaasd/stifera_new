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
        Schema::table('yudisium_periode', function (Blueprint $table) {
            $table->unsignedBigInteger('id_program_studi')->nullable()->after('nama_periode');
            $table->string('angkatan_allowed')->nullable()->after('id_program_studi')->comment('Comma-separated list of angkatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yudisium_periode', function (Blueprint $table) {
            $table->dropColumn(['id_program_studi', 'angkatan_allowed']);
        });
    }
};
