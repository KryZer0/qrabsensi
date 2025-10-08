<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
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

    public function fetchHistoryByKelas(Request $request) {
        $request->validate([
            'kelas' => 'required|string',
        ]);
        $kelas = $request->input('kelas');
        
        if (!in_array($kelas, ['X', 'XI', 'XII'])) {
            return response()->json(['error' => 'Kelas tidak valid.'], 400);
        }

        $history = AbsenModel::join('siswa', 'absensi.nisn', '=', 'siswa.nisn')
            ->select('siswa.nama', 'absensi.*')
            ->where('siswa.kelas', $request->input('kelas'))
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
        $filename = "presensi-{$monthParam}.xlsx";
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

        $filename = "Presensi-{$monthParam}-{$kelas}.xlsx";
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function exportRekapAbsensiExcel(Request $request, $kelas)
    {
        $monthParam = $request->query('bulan');
        $semester = $request->query('semester');

        if (!$monthParam && !$semester) {
            return response()->json(['error' => 'Harus pilih bulan (MM-YYYY) atau semester.'], 400);
        }

        $absens = absenModel::with('siswa')
            ->whereHas('siswa', function ($query) use ($kelas) {
                $query->where('kelas', $kelas);
            });

        if ($monthParam && preg_match('/^\d{2}-\d{4}$/', $monthParam)) {
            [$month, $year] = explode('-', $monthParam);
            $absens->whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year);
        }

        if ($semester) {
            $year = date('Y');
            if ($semester == 1) {
                $absens->whereBetween('tanggal', ["$year-01-01", "$year-06-30"]);
            } elseif ($semester == 2) {
                $absens->whereBetween('tanggal', ["$year-07-01", "$year-12-31"]);
            }
        }

        $dataAbsensi = $absens->get();

        $rekap = [];
        foreach ($dataAbsensi as $absen) {
            $nisn = $absen->nisn;
            $nama = optional($absen->siswa)->nama;

            if (!isset($rekap[$nisn])) {
                $rekap[$nisn] = [
                    'nisn' => $nisn,
                    'nama' => $nama,
                    'Hadir' => 0,
                    'Sakit' => 0,
                    'Izin'  => 0,
                    'Alfa'  => 0,
                    'Total' => 0,
                ];
            }

            switch (strtolower($absen->keterangan)) {
                case 'Masuk':
                    $rekap[$nisn]['Hadir']++;
                    break;
                case 'Telat':
                    $rekap[$nisn]['Hadir']++;
                    break;
                case 'Sakit':
                    $rekap[$nisn]['Sakit']++;
                    break;
                case 'Izin':
                    $rekap[$nisn]['Izin']++;
                    break;
                default:
                    $rekap[$nisn]['Alfa']++;
                    break;
            }

            $rekap[$nisn]['Total']++;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $logoPath = public_path('images/logo.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setPath($logoPath);
            $drawing->setHeight(70);
            $drawing->setCoordinates('A1');
            $drawing->setWorksheet($sheet);
        }

        $sheet->setCellValue('C1', 'SMK Gelora Industri');
        $sheet->mergeCells('C1:G1');
        $sheet->getStyle('C1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('C2', 'Laporan Rekapitulasi Pressensi');
        $sheet->mergeCells('C2:G2');
        $sheet->getStyle('C2')->getFont()->setSize(14);
        $sheet->getStyle('C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('C3', "Kelas: $kelas");
        $sheet->mergeCells('C3:G3');
        $sheet->getStyle('C3')->getFont()->setSize(12);
        $sheet->getStyle('C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('C4', "Bulan: $monthParam");
        $sheet->mergeCells('C4:G4');
        $sheet->getStyle('C4')->getFont()->setSize(12);
        $sheet->getStyle('C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $startRow = 5;
        $sheet->fromArray([
            'NISN', 'Nama Siswa', 'Hadir', 'Sakit', 'Izin', 'Alfa', 'Total Pertemuan'
        ], null, "A{$startRow}");

        $row = $startRow + 1;
        foreach ($rekap as $r) {
            $sheet->setCellValue("A{$row}", $r['nisn']);
            $sheet->setCellValue("B{$row}", $r['nama']);
            $sheet->setCellValue("C{$row}", $r['Hadir']);
            $sheet->setCellValue("D{$row}", $r['Sakit']);
            $sheet->setCellValue("E{$row}", $r['Izin']);
            $sheet->setCellValue("F{$row}", $r['Alfa']);
            $sheet->setCellValue("G{$row}", $r['Total']);
            $row++;
        }

        $periode = $monthParam ?: "semester-{$semester}";
        $filename = "rekap-presensi-{$periode}-{$kelas}.xlsx";

        // Bersihkan output buffer agar tidak ada gangguan
        if (ob_get_length()) {
            ob_end_clean();
        }

        $writer = new Xlsx($spreadsheet);

        // Gunakan stream langsung ke browser
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

}
