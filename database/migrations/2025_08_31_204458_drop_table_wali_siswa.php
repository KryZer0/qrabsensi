<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('wali_siswa');
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::create('wali_siswa', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};

