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
            $table->UnsignedInteger('nisn')->unique();
            $table->string('nama');
            $table->string('jns_kelamin');
            $table->string('kelas')->nullable();
            $table->enum('jurusan', ['Teknik Kendaraan Ringan', 'Teknik Mesin Industri', 'Administrasi Perkantoran']);
            $table->timestamps();

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
