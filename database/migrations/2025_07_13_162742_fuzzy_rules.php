<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fuzzy_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name');            // Contoh: "Rule 1"
            $table->string('output_himpunan');      // Contoh: "Kurang", "Cukup", "Baik"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fuzzy_rules');
    }
};
