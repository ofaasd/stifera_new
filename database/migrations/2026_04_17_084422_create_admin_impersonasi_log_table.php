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
        Schema::create('admin_impersonasi_log', function (Blueprint $table) {
            $table->id();
            $table->string('admin_usrnm', 100)->comment('username admin yang melakukan impersonasi');
            $table->string('nim', 50)->comment('NIM mahasiswa yang diimpersonasi');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('login_at')->useCurrent();
            $table->timestamp('logout_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_impersonasi_log');
    }
};
