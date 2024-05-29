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
        Schema::create('client', function (Blueprint $table) {
            $table->string('id')->primary(); 
            $table->uuid('id_user')->unique(); 
            $table->string('email2');
            $table->enum('jenis_kelamin', ['p', 'w']); // Jenis kelamin
            $table->date('tanggal_lahir'); // Tanggal lahir
            $table->integer('tinggi_badan'); // Tinggi badan
            $table->integer('berat_badan'); // Berat badan
            $table->string('golongan_darah'); // Golongan darah
            $table->string('alamat'); // Alamat
            $table->string('telepon')->nullable(); // Telepon
            $table->text('about_me')->nullable(); // About me
            $table->text('aktifitas')->nullable(); // About me
            $table->text('tujuan')->nullable(); // About me
            $table->text('medis')->nullable(); // About me
            $table->string('profile_foto')->nullable(); // Profile foto
            $table->enum('is_active', ['Y', 'N'])->default('Y');
            $table->timestamps(); // Created at dan Updated at

            // Relasi dengan tabel users
            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client');
    }
};
