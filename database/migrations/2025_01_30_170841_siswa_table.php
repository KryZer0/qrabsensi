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
        //
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->UnsignedBigInteger('nisn')->unique();
            $table->string('nama');
            $table->string('jns_kelamin');
            $table->UnsignedBigInteger('id_wali')->nullable();
            $table->timestamps();

            $table->foreign('id_wali')->references('id')->on('wali_siswa')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('siswa');
    }
};
