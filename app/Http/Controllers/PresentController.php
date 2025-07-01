<?php

namespace App\Http\Controllers;

use App\Events\SiswaAbsenEvent;
use App\Models\absenModel;
use App\Models\siswaModel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresentController extends Controller
{
    //  Fungsi absen masuk(check in)
    public function checkIn($nisn)
    {
        // Cek apakah siswa dengan nisn tersebut ada
        $siswa = siswaModel::where('nisn', $nisn)->first();
        if (!$siswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa tidak ditemukan'
            ], 404);
        }

        // Siapkan data untuk absensi
        $data['jam_masuk']  = date('H:i:s');
        $data['tanggal']    = date('Y-m-d');
        $data['nisn']       = $nisn;

        // Cek apakah hari ini adalah hari libur (Sabtu atau Minggu)
        if (date('l') == 'Saturday' || date('l') == 'Sunday') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hari Libur Tidak bisa Check In'
            ], 403);
        }

        if (strtotime($data['jam_masuk']) > strtotime(config('absensi.jam_pulang'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Waktu Check In Sudah Habis'
            ], 403);
        }

        // Tentukan status kehadiran berdasarkan jam masuk
        if (strtotime($data['jam_masuk']) >= strtotime(config('absensi.jam_masuk') . ' -1 hours')
        && strtotime($data['jam_masuk']) <= strtotime(config('absensi.jam_masuk'))) {
            $data['keterangan'] = 'Masuk';
        } else if (strtotime($data['jam_masuk']) > strtotime(config('absensi.jam_masuk')) && strtotime($data['jam_masuk']) <= strtotime(config('absensi.jam_pulang'))) {
            $data['keterangan'] = 'Telat';
        } else {
            $data['keterangan'] = 'Alpha';
        }

        // Cek apakah siswa sudah check-in hari ini
        $present = absenModel::where('nisn', $nisn)
                            ->where('tanggal', $data['tanggal'])
                            ->first();

        if ($present) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa sudah check-in hari ini'
            ], 403);
        }

        // Catat kehadiran siswa
        absenModel::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in berhasil'
        ], 200);
    }

    public function checkOut($nisn)
    {
        // Cek apakah siswa dengan nisn tersebut ada
        $siswa = siswaModel::where('nisn', $nisn)->first();
        if (!$siswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa tidak ditemukan'
            ], 404);
        }

        // Siapkan data untuk check-out
        $tanggal = date('Y-m-d');
        $data['jam_keluar'] = date('H:i:s');

        // Cek apakah siswa sudah check-in hari ini
        $present = absenModel::where('nisn', $nisn)
                            ->where('tanggal', $tanggal)
                            ->first();

        if (!$present) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa belum check-in hari ini'
            ], 400);
        }

        // Cek apakah siswa sudah check-out
        if ($present->jam_keluar !== null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda telah check-out sebelumnya'
            ], 400);
        }
        // Update data check-out
        $present->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Check-out berhasil'
        ], 200);
    }

    public function getSiswaByKelasJurusan(Request $request)
    {
        $kelas = $request->query('kelas');
        $jurusan = $request->query('jurusan');

        if (!$kelas || !$jurusan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kelas dan Jurusan harus diisi'
            ], 400);
        }
        
        $siswa = siswaModel::where('kelas', $kelas)
            ->where('jurusan', $jurusan)
            ->get(['nama', 'nisn']);

        return response()->json($siswa);
    }

    public function saveIzinSiswa(Request $request)
    {
        $nisn = $request->input('nisn');
        $keterangan = $request->input('keterangan');

        $siswa = siswaModel::where('nisn', $nisn)->first();
        if (!$siswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa tidak ditemukan'
            ], 404);
        }

        $data['tanggal'] = Carbon::now()->toDateString();
        $data['nisn'] = $nisn;
        $data['keterangan'] = $keterangan;

        if (date('l') == 'Saturday' || date('l') == 'Sunday') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hari Libur Tidak bisa Absen'
            ], 403);
        }
        
        $present = absenModel::where('nisn', $nisn)
                            ->where('tanggal', $data['tanggal'])
                            ->first();

        if ($present) {
            return response()->json([
                'status' => 'error',
                'message' => 'Siswa sudah Absen hari ini'
            ], 403);
        }

        absenModel::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Izin berhasil disimpan'
        ], 200);
    }
}
