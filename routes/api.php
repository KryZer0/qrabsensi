<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\QrCodeController;

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
    Route::get('/generate-qr/{nisn}', [QrCodeController::class, 'generateQrCode']);
    Route::get('/generate-qr-batch', [QrCodeController::class, 'generateQrBatch']);
    Route::get('/export-absensi', [HistoryController::class, 'exportAbsensiExcel']);

    Route::prefix('/siswa')->group(function() {
        Route::get('/fetch', [SiswaController::class, 'fetchSiswa']);
        Route::post('/store', [SiswaController::class, 'store']);
        Route::post('/store-batch', [SiswaController::class, 'storeBatch']);
        Route::put('/update/{nisn}', [SiswaController::class, 'update']);
        Route::get('/qrcodes/{nisn}', [SiswaController::class, 'generateAbsensiQr']);
        Route::get('/qrcodesbatch', [SiswaController::class, 'generateAbsensiQrBatch']);
        Route::get('/generatecard/{nisn}', [QrCodeController::class, 'fetchQrCode']);
        Route::get('/generatecardbatch', [QrCodeController::class, 'fetchAllQrCodes']);

        Route::get('/fetch-kelas-jurusan', [PresentController::class, 'getSiswaByKelasJurusan']);
        Route::post('/save-izin', [PresentController::class, 'saveIzinSiswa']);
    });
    
});

