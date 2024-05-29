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
        Schema::create('client_body_composition', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_client');
            $table->integer('result_day');
            $table->decimal('weigth', 10, 2);
            $table->decimal('body_fat', 10, 2);
            $table->decimal('body_water', 10, 2);
            $table->decimal('muscle_mass', 10, 2);
            $table->decimal('physical_rating', 10, 2);
            $table->decimal('bmr', 10, 2);
            $table->decimal('metabolic_age', 10, 2);
            $table->decimal('bone_mass', 10, 2);
            $table->decimal('visceral_fat', 10, 2);
            $table->date('date_actual');
            $table->timestamps();

            $table->foreign('id_client')->references('id')->on('client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_body_composition');
    }
};
