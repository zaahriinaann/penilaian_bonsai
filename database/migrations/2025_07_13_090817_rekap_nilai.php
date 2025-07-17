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
        Schema::create('rekap_nilai', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kontes');
            $table->unsignedBigInteger('id_bonsai');
            $table->float('skor_akhir');
            $table->string('himpunan_akhir');
            $table->integer('peringkat')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekap_nilai');
    }
};
