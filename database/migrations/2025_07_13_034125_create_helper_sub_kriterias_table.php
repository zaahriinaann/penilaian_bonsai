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
        Schema::create('helper_sub_kriteria', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_kriteria')->unsigned();
            $table->string('kriteria');
            $table->bigInteger('id_sub_kriteria')->unsigned();
            $table->string('sub_kriteria');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helper_sub_kriteria');
    }
};
