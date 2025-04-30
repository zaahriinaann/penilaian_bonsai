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
        Schema::create('bonsai', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nama_pohon');
            $table->string('nama_lokal');
            $table->string('nama_latin');
            $table->string('ukuran');
            $table->string('ukuran_1');
            $table->string('ukuran_2');
            $table->string('format_ukuran');
            $table->string('no_induk_pohon')->unique();
            $table->string('masa_pemeliharaan');
            $table->string('format_masa');
            $table->string('pemilik');
            $table->string('no_anggota');
            $table->string('cabang');
            $table->string('foto')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonsai');
    }
};
