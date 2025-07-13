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
        Schema::create('helper_himpunan', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_kriteria');
            $table->string('kriteria');
            $table->bigInteger('id_sub_kriteria');
            $table->string('sub_kriteria');
            $table->bigInteger('id_himpunan');
            $table->string('himpunan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helper_himpunan');
    }
};
