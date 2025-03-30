<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use App\Models\siswaModel;

class QrCodeController extends Controller
{
    public function generateQrCode($nisn)
    {
        // Pastikan folder 'public/qr-codes' ada, jika tidak buat foldernya
        $folderPath = public_path('qr-codes');
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true, true);
        }

        // Nama file QR Code
        $fileName = $nisn . '.png';
        $filePath = $folderPath . '/' . $fileName;

        // **Validasi jika QR Code sudah ada**
        if (File::exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code untuk ' . $nisn . ' telah digenerate sebelumnya',
                'qr_code_url' => url('qr-codes/' . $fileName),
            ], 409); // HTTP 409 Conflict
        }

        // Buat QR Code
        $qrCode = QrCode::create($nisn)
            ->setSize(300)
            ->setMargin(10);

        // Simpan QR Code sebagai PNG
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        file_put_contents($filePath, $result->getString());

        // URL akses QR Code
        $qrCodeUrl = url('qr-codes/' . $fileName);

        return response()->json([
            'success' => true,
            'message' => 'QR Code berhasil dibuat',
            'qr_code_url' => $qrCodeUrl,
        ]);
    }

    public function generateQrBatch()
    {
        $folderPath = public_path('qr-codes');
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true, true);
        }

        // Ambil semua data NISN dari tabel siswa
        $siswaList = siswaModel::select('nisn')->get();

        $generatedQrCodes = [];
        $skippedQrCodes = [];

        foreach ($siswaList as $siswa) {
            $nisn = $siswa->nisn;
            $fileName = $nisn . '.png';
            $filePath = $folderPath . '/' . $fileName;

            if (File::exists($filePath)) {
                $skippedQrCodes[] = [
                    'nisn' => $nisn,
                    'qr_code_url' => url('qr-codes/' . $fileName),
                    'message' => 'QR Code sudah ada'
                ];
                continue;
            }

            $qrCode = QrCode::create($nisn)->setSize(300)->setMargin(10);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            file_put_contents($filePath, $result->getString());

            $generatedQrCodes[] = [
                'nisn' => $nisn,
                'qr_code_url' => url('qr-codes/' . $fileName),
                'message' => 'QR Code berhasil dibuat'
            ];
        }

        return response()->json([
            'success' => true,
            'generated' => $generatedQrCodes,
            'skipped' => $skippedQrCodes
        ]);
    }

    public function fetchQrCode($nisn)
    {
        $siswa = siswaModel::where('nisn', $nisn)->first();
        if (!$siswa) {
            return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
        }

        $folderPath = public_path('qr-codes');
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true, true);
        }
        
        $fileName = $nisn . '.png';
        $filePath = $folderPath . '/' . $fileName;

        if (!File::exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code untuk ' . $nisn . ' tidak ditemukan',
            ], 404); // HTTP 404 Not Found
        }

        return response()->json([
            'nisn' => $siswa->nisn,
            'nama' => $siswa->nama,
            'qr_code' => url('qr-codes/' . $siswa->nisn . '.png') // Path QR Code
        ]);
    }

    public function fetchAllQrCodes()
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
