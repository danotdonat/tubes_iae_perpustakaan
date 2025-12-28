<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Handle Login Request
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->only('username', 'password');

        // 2. Cek Login menggunakan JWTAuth Facade
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau Password salah!'
            ], 401);
        }

        // 3. Jika Berhasil, kembalikan Token dan data User
        return response()->json([
            'success' => true,
            'user'    => auth('api')->user(),
            'token'   => $token,
            'expires_in' => config('jwt.ttl') * 60 // Durasi token dalam detik
        ], 200);
    }

    /**
     * Handle Logout Request
     */
    public function logout()
    {
        // Menghapus token (Invalidate)
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Logout!'
        ]);
    }
}
