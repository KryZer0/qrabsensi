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
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropForeign(['id_wali']); // hapus foreign key kalau ada
            $table->dropColumn('id_wali');    // hapus kolom
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('siswa', function (Blueprint $table) {
            $table->unsignedBigInteger('id_wali')->nullable();

            $table->foreign('id_wali')->references('id')->on('wali_siswa')->onDelete('cascade');
        });
    }
};
