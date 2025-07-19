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
        Schema::create('helper_domain', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_kriteria')->unsigned();
            $table->string('kriteria');
            $table->bigInteger('id_sub_kriteria')->unsigned()->nullable();
            $table->string('sub_kriteria')->nullable();
            $table->bigInteger('id_himpunan')->unsigned();
            $table->string('himpunan');
            $table->bigInteger('id_domain')->unsigned();
            $table->float('domain_min')->nullable();
            $table->float('domain_max')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('helper_domain');
    }
};
