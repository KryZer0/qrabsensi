<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresentController;
use App\Http\Controllers\HistoryController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/check/{nisn}', [PresentController::class, 'checkIn']);
Route::patch('/checkout/{nisn}', [PresentController::class, 'checkOut']);
Route::get('/history', [HistoryController::class, 'fetchHistory']);