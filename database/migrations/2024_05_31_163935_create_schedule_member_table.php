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
        Schema::create('schedule_member', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_schedule');
            $table->string('id_client');
            $table->timestamps();
            $table->enum('is_active', ['Y', 'N'])->default('Y');

            $table->foreign('id_schedule')->references('id')->on('schedule');
            $table->foreign('id_client')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_member');
    }
};
