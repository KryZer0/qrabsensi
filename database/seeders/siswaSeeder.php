<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\siswaModel;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        $namaSiswa = [
            'Anisa Balqis',
            'Cinta Putri Rahayu',
            'Maida Yahshfani Zuhurfi',
            'Sion Renata Marbun',
            'Adi Taj Nugroho',
            'Devin Rizky Ramadhan',
            'Muhamad Abdul Zikri Zakaria',
            'Rasya Andika Pratama',
            'Saipul',
            'Satria Putra Andhika',
            'Satria Rangga',
            'Steven Galih Revaldo',
            'Syafit',
            'Adnan Setiawan',
            'Alfian Hanif Fauzan',
            'Alvi Ramadan',
            'Barisa Nuriajar',
            'Dimas Catur Prasetyo Mukti',
            'Erwin Maulana',
            'Fahri Pratama Ramadhan',
            'Muhammad Dika Febrian',
            'Razia Jantika',
            'Rizky Harfiansha'
        ];

        foreach ($namaSiswa as $nama) {
            siswaModel::create([
                'nisn' => fake()->unique()->numerify('########'),
                'nama' => $nama,
                'jns_kelamin' => fake()->randomElement(['L', 'P']),
                'kelas' => fake()->randomElement(['X', 'XI', 'XII']),
                'jurusan' => fake()->randomElement([
                    'Teknik Kendaraan Ringan',
                    'Teknik Mesin Industri',
                    'Administrasi Perkantoran'
                ]),
            ]);
        }
    }
}
