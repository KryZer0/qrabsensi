<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::with('role')->where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'email atau password salah'], 401);
        }
        $nama = $user->name;
        $id_role = $user->id_role;
    
        return response()->json([
            'message' => 'Login berhasil',
            'nama' => $nama,
            'id_role' => $id_role,
        ]);
    }
}
