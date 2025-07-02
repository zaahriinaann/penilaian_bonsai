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
        Schema::create('pendaftaran_kontes', function (Blueprint $table) {
            $table->id();
            $table->string('kontes_id');
            $table->string('user_id');
            $table->string('bonsai_id');
            $table->string('kelas');
            $table->string('nomor_pendaftaran')->default(1);
            $table->string('nomor_juri');
            $table->string('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_kontes');
    }
};
