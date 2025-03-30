<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\siswaModel;
use Illuminate\Support\Facades\DB;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // SiswaModel::factory(10)->create();
        DB::table('siswa')->insert([
            'nisn'      => '123456789',
            'nama'       => 'ahmad',
            'jns_kelamin'  => 'L',
            'created_at' => now(),
        ]);
        DB::table('siswa')->insert([
            'nisn'      => '123123123',
            'nama'       => 'kryzer',
            'jns_kelamin'  => 'L',
            'created_at' => now(),
        ]);
    }
}
