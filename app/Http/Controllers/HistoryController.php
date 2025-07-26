<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\User;
use App\Models\absenModel;

class HistoryController extends Controller
{
    //
    public function fetchHistory(Request $request) {
        $history = AbsenModel::join('siswa', 'absensi.nisn', '=', 'siswa.nisn')
            ->select('siswa.nama', 'absensi.*')
            ->orderBy('tanggal', 'desc')
            ->paginate($request->input('per_page', 10));
    
        return response()->json($history);
    }

    public function exportAbsensiExcel(Request $request)
    {
        $monthParam = $request->query('month');

        if (!$monthParam || !preg_match('/^\d{2}-\d{4}$/', $monthParam)) {
            return response()->json(['error' => 'Format bulan tidak valid. Gunakan MM-YYYY.'], 400);
        }

        [$month, $year] = explode('-', $monthParam);

        $absens = absenModel::with('siswa')
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal', 'desc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            'NISN', 'Nama Siswa', 'Kelas', 'Jurusan', 'Tanggal', 'Keterangan', 'Jam Masuk', 'Jam Keluar'
        ], null, 'A1');

        $row = 2;
        foreach ($absens as $absen) {
            $sheet->setCellValue("A{$row}", $absen->nisn);
            $sheet->setCellValue("B{$row}", optional($absen->siswa)->nama);
            $sheet->setCellValue("C{$row}", optional($absen->siswa)->kelas);
            $sheet->setCellValue("D{$row}", optional($absen->siswa)->jurusan);
            $sheet->setCellValue("E{$row}", $absen->tanggal);
            $sheet->setCellValue("F{$row}", $absen->keterangan);
            $sheet->setCellValue("G{$row}", $absen->jam_masuk);
            $sheet->setCellValue("H{$row}", $absen->jam_keluar);
            $row++;
        }

        // Ganti tempnam dengan path eksplisit
        $filename = "absensi-{$monthParam}.xlsx";
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($tempPath)) {
            unlink($tempPath); // Hapus file lama
        }

        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function exportAbsensiExcelByKelas(Request $request, $kelas)
    {
        $monthParam = $request->query('bulan');

        if (!$monthParam || !preg_match('/^\d{2}-\d{4}$/', $monthParam)) {
            return response()->json(['error' => 'Format bulan tidak valid. Gunakan MM-YYYY.'], 400);
        }

        [$month, $year] = explode('-', $monthParam);

        $absens = absenModel::with('siswa')
            ->whereHas('siswa', function ($query) use ($kelas) {
                $query->where('kelas', $kelas);
            })
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal', 'desc')
            ->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            'NISN', 'Nama Siswa', 'Kelas', 'Jurusan', 'Tanggal', 'Keterangan', 'Jam Masuk', 'Jam Keluar'
        ], null, 'A1');

        $row = 2;
        foreach ($absens as $absen) {
            $sheet->setCellValue("A{$row}", $absen->nisn);
            $sheet->setCellValue("B{$row}", optional($absen->siswa)->nama);
            $sheet->setCellValue("C{$row}", optional($absen->siswa)->kelas);
            $sheet->setCellValue("D{$row}", optional($absen->siswa)->jurusan);
            $sheet->setCellValue("E{$row}", $absen->tanggal);
            $sheet->setCellValue("F{$row}", $absen->keterangan);
            $sheet->setCellValue("G{$row}", $absen->jam_masuk);
            $sheet->setCellValue("H{$row}", $absen->jam_keluar);
            $row++;
        }

        $filename = "absensi-{$monthParam}-{$kelas}.xlsx";
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

}
