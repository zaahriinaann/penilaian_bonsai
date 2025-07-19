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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('no_induk_juri')->nullable();
            $table->string('nama_juri');
            $table->string('foto')->nullable();
            $table->string('sertifikat');
            $table->string('no_telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('username')->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('status')->default('1');
            $table->string('role')->default('juri');
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
