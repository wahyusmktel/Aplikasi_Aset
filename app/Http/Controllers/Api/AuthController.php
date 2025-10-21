<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user login via API.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required', // Nama perangkat (misal: "Samsung A52")
        ]);

        // Coba autentikasi user
        if (! Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak cocok.'],
            ]);
        }

        $user = $request->user();

        // Pastikan hanya user biasa (pegawai) yang bisa login via API ini
        // if ($user->isAdmin()) {
        //     Auth::logout(); // Logout jika admin
        //     throw ValidationException::withMessages([
        //         'email' => ['Admin tidak diizinkan login via API ini.'],
        //     ]);
        // }

        // Buat token Sanctum
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [ // Kirim info dasar user jika perlu
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // Tambahkan info employee jika perlu: 'employee_position' => $user->employee?->position
            ]
        ]);
    }

    /**
     * Handle user logout via API.
     */
    public function logout(Request $request)
    {
        // Hapus token saat ini yang digunakan untuk request
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }

    /**
     * Get authenticated user details.
     */
    public function user(Request $request)
    {
        return response()->json($request->user()->load('employee')); // Kirim data user + employee
    }
}
