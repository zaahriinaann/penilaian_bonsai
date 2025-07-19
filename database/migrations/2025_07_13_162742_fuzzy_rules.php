<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuzzy_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kriteria');
            $table->unsignedBigInteger('id_sub_kriteria')->nullable(); // dibuat nullable agar bisa multi-sub
            $table->json('input_himpunan');
            $table->string('output_himpunan');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('id_kriteria')->references('id')->on('helper_kriteria')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fuzzy_rules');
    }
};
