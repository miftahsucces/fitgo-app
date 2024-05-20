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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('jenis_kelamin', ['p', 'w']);
            $table->date('tanggal_lahir');       
            $table->string('alamat');
            $table->string('telepon')->nullable();
            $table->string('email')->unique(); 
            $table->timestamps();

            // Relasi dengan tabel users
            $table->foreign('email')->references('email')->on('users')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
