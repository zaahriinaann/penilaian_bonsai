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
        Schema::create('defuzzifikasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kontes');
            $table->unsignedBigInteger('id_bonsai');
            $table->unsignedBigInteger('id_juri');
            $table->unsignedBigInteger('id_kriteria');
            $table->float('hasil_defuzzifikasi', 8, 2);
            $table->unsignedBigInteger('id_hasil_himpunan')->nullable();
            $table->string('hasil_himpunan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defuzzifikasis');
    }
};
