<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hasil_fuzzy_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kontes')->index();
            $table->unsignedBigInteger('id_bonsai')->index();
            $table->unsignedBigInteger('id_juri')->index();
            $table->unsignedBigInteger('id_kriteria')->index();
            $table->unsignedBigInteger('fuzzy_rule_id')->index()->nullable();
            $table->decimal('alpha', 5, 4)->nullable();
            $table->decimal('z_value', 8, 2)->nullable();
            $table->timestamps();

            $table->foreign('fuzzy_rule_id')
                ->references('id')->on('fuzzy_rules')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_fuzzy_rules');
    }
};
