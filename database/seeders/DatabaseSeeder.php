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

        DB::table('siswa')->insert([
            // 'nisn'      => '109210940086',
            'nisn'      => '940086',
            'nama'       => 'Dewi Safitri',
            'jns_kelamin'  => 'P',
            'kelas' => 'X',
            'jurusan' => 'Manajemen Perkantoran',
            'created_at' => now(),
        ]);
        DB::table('siswa')->insert([
            // 'nisn'      => '103829373828',
            'nisn'      => '373828',
            'nama'       => 'Yoga Chandra',
            'jns_kelamin'  => 'L',
            'kelas' => 'X',
            'jurusan' => 'Teknik Kendaraan Ringan',
            'created_at' => now(),
        ]);

        $this->call([
            SiswaSeeder::class,
            AbsensiSeeder::class,
        ]);
    }
}
