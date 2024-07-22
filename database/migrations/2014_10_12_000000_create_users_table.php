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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary(); // ID anggota sebagai UUID
            $table->string('full_name');
            $table->string('email')->unique();
            $table->integer('tipe_user'); //1 : admin, 2: user
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('is_active', ['Y', 'N'])->default('Y');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
