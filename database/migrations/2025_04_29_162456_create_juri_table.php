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
        Schema::create('juri', function (Blueprint $table) {
            $table->id();
            $table->string('no_induk_juri')->nullable();
            $table->string('nama_juri');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('slug')->unique();
            $table->string('no_telepon')->nullable();
            $table->string('status')->default('active');
            $table->string('role')->default('juri');
            $table->string('foto')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juri');
    }
};
