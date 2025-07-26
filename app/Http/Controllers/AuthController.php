<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

    public function tambahGuru(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'  => ['required', 'regex:/^[^\d]+$/'],
            'email' => 'required|email|unique:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $guru = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'id_role' => 2,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data guru berhasil disimpan',
            'data' => $guru
        ]);
    }

    public function fetchGuru(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $gurus = User::paginate($perPage);

        return response()->json($gurus);
    }

    public function updateGuru(Request $request, $id)
    {
        $guru = User::find($id);
        if (!$guru) {
            return response()->json(['message' => 'Guru tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $guru->update($validated);

        return response()->json([
            'message' => 'Data guru berhasil diperbarui',
            'data' => $guru
        ]);
    }


    public function deleteGuru($id)
    {
        $guru = User::find($id);
        if (!$guru) {
            return response()->json(['message' => 'Guru tidak ditemukan'], 404);
        }

        $guru->delete();
        return response()->json(['message' => 'Data guru berhasil dihapus']);
    }
}
