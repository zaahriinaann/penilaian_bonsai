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
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_kontes')->unsigned()->index();
            $table->bigInteger('id_peserta')->unsigned()->index();
            $table->bigInteger('id_juri')->unsigned()->index();
            $table->bigInteger('id_bonsai')->unsigned()->index();
            $table->bigInteger('id_kriteria_penilaian')->unsigned()->index();
            $table->double('d_keanggotaan', 8, 2)->default(0);
            $table->double('defuzzifikasi', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};
