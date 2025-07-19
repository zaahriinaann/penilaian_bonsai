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
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->string('kriteria');      // contoh: keseimbangan_optik
            $table->string('sub_kriteria');
            $table->string('himpunan');      // A, B, C, atau D
            $table->unsignedTinyInteger('min'); // nilai minimum
            $table->unsignedTinyInteger('max'); // nilai maksimum
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};
