<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hasil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kontes')->constrained('kontes')->onDelete('cascade');
            $table->foreignId('id_bonsai')->constrained('bonsai')->onDelete('cascade');
            $table->foreignId('id_kriteria')->constrained('helper_kriteria')->onDelete('cascade');
            $table->double('hasil_defuzzifikasi');
            $table->string('hasil_himpunan');
            $table->unsignedBigInteger('id_hasil_himpunan')->nullable();
            $table->timestamps();

            $table->unique(['id_kontes', 'id_bonsai', 'id_kriteria']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil');
    }
};
