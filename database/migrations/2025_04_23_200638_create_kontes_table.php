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
        Schema::create('kontes', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kontes');
            $table->string('slug')->unique();
            $table->string('tempat_kontes');
            $table->string('tingkat_kontes');
            $table->string('link_gmaps')->nullable();
            $table->datetime('tanggal_mulai_kontes');
            $table->datetime('tanggal_selesai_kontes');
            $table->bigInteger('jumlah_peserta');
            $table->bigInteger('limit_peserta');
            $table->bigInteger('harga_tiket_kontes');
            $table->string('status')->default(1);
            $table->string('poster_kontes')->nullable();
            $table->timestamps();
            $table->softDeletes()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontes');
    }
};
