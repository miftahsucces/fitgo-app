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
        Schema::create('trainer_spesialis', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('id_trainer'); 
            $table->string('spesialis'); 
            $table->string('desc')->nullable(); 
            $table->enum('is_active', ['Y', 'N'])->default('Y');
            $table->timestamps();

            // Relasi dengan tabel users_detail
            $table->foreign('id_trainer')->references('id')->on('trainer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_spesialis');
    }
};
