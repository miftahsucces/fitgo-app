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
        Schema::create('client_program', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_client');
            $table->uuid('id_program');
            $table->timestamps();
            $table->enum('is_active', ['Y', 'N'])->default('Y');

            $table->foreign('id_client')->references('id')->on('client');
            $table->foreign('id_program')->references('id')->on('program');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_program');
    }
};