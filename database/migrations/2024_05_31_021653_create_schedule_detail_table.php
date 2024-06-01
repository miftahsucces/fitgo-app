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
        Schema::create('schedule_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_schedule');
            $table->string('location');
            $table->date('date_schedule');
            $table->time('time_start');
            $table->time('time_end');
            $table->timestamps();
            $table->enum('is_active', ['Y', 'N'])->default('Y');

            $table->foreign('id_schedule')->references('id')->on('schedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_detail');
    }
};
