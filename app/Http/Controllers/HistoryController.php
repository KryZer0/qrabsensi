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
            ->paginate($request->input('per_page', 10)); // Default 10 items per page
    
        return response()->json($history);
    }
}
