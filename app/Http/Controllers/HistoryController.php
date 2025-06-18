<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}
