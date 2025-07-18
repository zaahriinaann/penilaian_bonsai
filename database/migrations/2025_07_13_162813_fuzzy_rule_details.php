<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuzzy_rule_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fuzzy_rule_id');
            $table->string('input_variable');
            $table->string('himpunan');
            $table->timestamps();

            $table->foreign('fuzzy_rule_id')
                ->references('id')
                ->on('fuzzy_rules')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fuzzy_rule_details');
    }
};
