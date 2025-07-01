<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\absenModel;
use App\Models\siswaModel;
use Illuminate\Support\Carbon;

class AbsensiSeeder extends Seeder
{
    public function run()
    {
        $siswaList = siswaModel::all();

        if ($siswaList->isEmpty()) {
            $this->command->warn('Tidak ada data siswa ditemukan. Jalankan SiswaSeeder terlebih dahulu.');
            return;
        }

        foreach ($siswaList as $siswa) {
            // Generate antara 3 sampai 7 absensi per siswa
            $jumlahAbsen = rand(3, 7);

            for ($i = 0; $i < $jumlahAbsen; $i++) {
                $tanggal = Carbon::now()->subDays(rand(1, 30))->format('Y-m-d');
                $jamMasuk = Carbon::createFromTime(rand(6, 8), rand(0, 59), 0)->format('H:i:s');
                $jamKeluar = Carbon::createFromTime(rand(14, 16), rand(0, 59), 0)->format('H:i:s');

                absenModel::create([
                    'nisn' => $siswa->nisn,
                    'tanggal' => $tanggal,
                    'keterangan' => fake()->randomElement(['Masuk','Alpha','Telat','Sakit','Izin']),
                    'jam_masuk' => $jamMasuk,
                    'jam_keluar' => $jamKeluar,
                ]);
            }
        }
    }
}
