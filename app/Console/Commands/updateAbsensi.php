<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class updateAbsensi extends Command
{
    protected $signature = 'absensi:update-checkout';
    protected $description = 'Update jam_keluar jika siswa belum checkout dan set alpha jika tidak hadir';

    public function handle()
    {
        $tanggalHariIni = Carbon::now()->toDateString();
        $waktuDefault = '20:00:00';

        //  Update siswa yang belum checkout
        DB::table('absensi')
            ->whereNull('jam_keluar')
            ->whereDate('tanggal', $tanggalHariIni)
            ->update(['jam_keluar' => $waktuDefault]);

        // 2. Ambil semua siswa
        $siswa = DB::table('siswa')->pluck('nisn');

        // 3. Ambil siswa yang sudah ada di absensi hari ini
        $siswaAbsenHariIni = DB::table('absensi')
            ->whereDate('tanggal', $tanggalHariIni)
            ->pluck('nisn');

        // 4. Cari siswa yang belum absen
        $siswaBelumAbsen = $siswa->diff($siswaAbsenHariIni);

        // 5. Insert siswa yang belum absen ke tabel absensi dengan status Alpha
        $dataAbsensi = $siswaBelumAbsen->map(function ($nisn) use ($tanggalHariIni) {
            return [
                'nisn'       => $nisn,
                'keterangan' => 'Alpha',
                'tanggal'    => $tanggalHariIni,
                'jam_masuk'  => '00:00:00',
                'jam_keluar' => '00:00:00',
            ];
        })->toArray();

        if (!empty($dataAbsensi)) {
            DB::table('absensi')->insert($dataAbsensi);
            $this->info(count($dataAbsensi) . ' siswa tidak hadir, ditandai sebagai Alpha.');
        } else {
            $this->info('Semua siswa sudah absen hari ini.');
        }
        $this->info('Absensi diperbarui: checkout otomatis dan Alpha untuk yang tidak hadir.');
    }
}
