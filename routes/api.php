<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// tambahkan /nisn/ sebelum check, checkout, dan generate-qr
// tambahkan /nisn/ sebelum fetch, store, update, dan qrcodes
// tambahkan /nisn/ sebelum nisn

Route::prefix('v1')->group(function () {
    Route::post('/check/{nisn}', [PresentController::class, 'checkIn']);
    Route::patch('/checkout/{nisn}', [PresentController::class, 'checkOut']);
    Route::get('/history', [HistoryController::class, 'fetchHistory']);
    Route::get('/history/kelas', [HistoryController::class, 'fetchHistoryByKelas']);
    Route::get('/generate-qr/{nisn}', [QrCodeController::class, 'generateQrCode']);
    Route::get('/generate-qr-batch', [QrCodeController::class, 'generateQrBatch']);
    Route::get('/export-absensi', [HistoryController::class, 'exportAbsensiExcel']);
    Route::get('/export-absensi/{kelas}', [HistoryController::class, 'exportAbsensiExcelByKelas']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('change-password', [AuthController::class, 'changePassword']);

    Route::prefix('/siswa')->group(function() {
        Route::get('/fetch', [SiswaController::class, 'fetchSiswa']);
        Route::get('/fetch/kelas', [SiswaController::class, 'fetchSiswaByKelas']);
        Route::post('/store', [SiswaController::class, 'store']);
        Route::post('/store-batch', [SiswaController::class, 'storeBatch']);
        Route::put('/update/{nisn}', [SiswaController::class, 'update']);
        Route::delete('/{nisn}', [SiswaController::class, 'destroy']);
        Route::get('/qrcodes/{nisn}', [SiswaController::class, 'generateAbsensiQr']);
        Route::get('/qrcodesbatch', [SiswaController::class, 'generateAbsensiQrBatch']);
        Route::get('/generatecard/{nisn}', [QrCodeController::class, 'fetchQrCode']);
        Route::get('/generatecardbatch', [QrCodeController::class, 'fetchAllQrCodes']);

        Route::get('/fetch-kelas-jurusan', [PresentController::class, 'getSiswaByKelasJurusan']);
        Route::post('/save-izin', [PresentController::class, 'saveIzinSiswa']);
    });

    Route::prefix('/guru')->group(function() {
        Route::post('/tambah', [AuthController::class, 'tambahGuru']);
        Route::get('/fetch', [AuthController::class, 'fetchGuru']);
        Route::put('/{id}', [AuthController::class, 'updateGuru']);
        Route::delete('/{id}', [AuthController::class, 'deleteGuru']);
        Route::post('/reset/{id}', [AuthController::class, 'resetPassword']);
    });
    
});

