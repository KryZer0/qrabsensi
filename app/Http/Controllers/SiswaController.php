<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\siswaModel;

class SiswaController extends Controller
{
    // Mengambil data siswa
    // public function fetchSiswa() {
    //     $siswa = SiswaModel::all();
    
    //     return response()->json($siswa);
    // }

    // Mengambil data siswa
    public function fetchSiswa(Request $request) {
        $siswa = SiswaModel::select('siswa.*')->paginate($request->input('per_page', 10));
    
        return response()->json($siswa);
    }

    public function fetchSiswaByKelas(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string|in:X,XI,XII',
        ]);

        $kelas = $request->query('kelas');

        $siswa = siswaModel::where('kelas', $kelas)
            ->paginate($request->query('per_page', 10));

        return response()->json($siswa);
    }

    // Menyimpan data siswa baru
    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|unique:siswa,nisn',
            'nama' => 'required',
            'jns_kelamin' => 'required',
            'kelas' => 'nullable',
            'jurusan' => 'required|in:Teknik Kendaraan Ringan,Teknik Mesin Industri,Manajemen Perkantoran',
            'id_wali' => 'nullable|exists:wali,id',
        ]);

        $siswa = siswaModel::create($request->all());
        return response()->json($siswa, 201);
    }

    public function storeBatch(Request $request)
    {
        // Validasi file yang diunggah
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        // Simpan file sementara
        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        // Buka file CSV dan baca isinya
        $csvData = array_map('str_getcsv', file($filePath));

        // Pastikan file tidak kosong dan memiliki header
        if (count($csvData) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'File CSV kosong atau format tidak valid.',
            ], 400);
        }

        // Ambil header dari file CSV dan pastikan formatnya benar
        $header = array_map('trim', $csvData[0]);
        $rows = array_slice($csvData, 1); // Ambil data tanpa header

        // Pastikan semua header sesuai dengan kolom di database
        $expectedColumns = ['nisn', 'nama', 'jns_kelamin', 'kelas', 'jurusan']; // Sesuaikan dengan database
        if (array_diff($expectedColumns, $header)) {
            return response()->json([
                'success' => false,
                'message' => 'Format header CSV tidak sesuai. Harus: ' . implode(', ', $expectedColumns),
            ], 400);
        }

        $dataToInsert = [];
        $nisnList = [];

        foreach ($rows as $row) {
            // Pastikan jumlah kolom sesuai dengan header
            if (count($row) == count($header)) {
                $data = array_combine($header, $row);

                // Validasi data sebelum insert
                if (!is_numeric($data['nisn']) || empty($data['nama']) || empty($data['jns_kelamin'])) {
                    continue; // Skip data yang tidak valid
                }

                $nisnList[] = $data['nisn']; // Simpan semua NISN yang diinput
                $dataToInsert[] = [
                    'nisn'  => $data['nisn'],
                    'nama'  => $data['nama'],
                    'jns_kelamin' => $data['jns_kelamin'],
                    'kelas' => $data['kelas'] ?? null,
                    'jurusan' => $data['jurusan'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Validasi jika tidak ada data yang bisa disimpan
        if (empty($dataToInsert)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data valid untuk disimpan.',
            ], 400);
        }

        // Cek apakah ada data yang sudah ada di database berdasarkan nisn
        $existingNisn = siswaModel::whereIn('nisn', $nisnList)->pluck('nisn')->toArray();

        if (!empty($existingNisn)) {
            return response()->json([
                'success' => false,
                'message' => 'Data duplikat ditemukan. NISN berikut sudah ada di database: ' . implode(', ', $existingNisn),
            ], 400);
        }

        // Simpan data dalam batch untuk efisiensi
        siswaModel::insert($dataToInsert);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan.',
            'total_inserted' => count($dataToInsert),
        ]);
    }

    // Menampilkan data siswa berdasarkan ID
    public function show($id)
    {
        $siswa = siswaModel::find($id);
        if (!$siswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }
        return response()->json($siswa);
    }

    // Memperbarui data siswa
    public function update(Request $request, $nisn)
    {
        $siswa = siswaModel::where('nisn',$nisn);
        if (!$siswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        $request->validate([
            'nisn' => 'required|unique:siswa,nisn,' . $nisn . ',nisn',
            'nama' => 'required',
            'jns_kelamin' => 'required',
            'kelas' => 'nullable',
            'jurusan' => 'required|in:Teknik Kendaraan Ringan,Teknik Mesin Industri,Manajemen Perkantoran',
            'id_wali' => 'nullable|exists:wali,id',
        ]);

        // except untuk mengabaikan field yang tidak ingin diupdate
        // karena field qr_code ada pada model aplikasi android
        // yang berfungsi melakukan generate/fetch qr code
        $siswa->update($request->except('qr_code'));
        return response()->json($siswa);
    }

    // Menghapus data siswa
    public function destroy($nisn)
    {
        $deleted = siswaModel::where('nisn', $nisn)->delete();

        if ($deleted === 0) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        return response()->json(['message' => 'Siswa berhasil dihapus']);
    }

    // Generate Kartu Absensi
    public function generateAbsensiQr($nisn)
    {
        $siswa = siswaModel::where('nisn', $nisn)->first();
        if (!$siswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }
        return response()->json([
            'nisn' => $siswa->nisn,
            'nama' => $siswa->nama,
            'qr_code' => url('qr-codes/' . $siswa->nisn . '.png') // Path QR Code
        ]);
    }

    // Generate Kartu Absensi (Batch)
    public function generateAbsensiQrBatch()
    {
        $siswa = siswaModel::all();
        
        return response()->json($siswa->map(function ($s) {
            return [
                'nisn' => $s->nisn,
                'nama' => $s->nama,
                'qr_code' => url('qr-codes/' . $s->nisn . '.png') // Path QR Code
            ];
        }));
    }
}