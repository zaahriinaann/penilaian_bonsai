<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fuzzy_rule_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fuzzy_rule_id')->constrained('fuzzy_rules')->onDelete('cascade');
            $table->string('input_variable');      // Contoh: "Keseimbangan Optik"
            $table->string('himpunan');            // Contoh: "Cukup", "Baik", "Kurang"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fuzzy_rule_details');
    }
};
