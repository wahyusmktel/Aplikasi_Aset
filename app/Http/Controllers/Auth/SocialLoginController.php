<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User; // Import User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Laravel\Socialite\Facades\Socialite; // Import Socialite
use Exception; // Import Exception class
use Illuminate\Support\Facades\Log;

class SocialLoginController extends Controller
{
    /**
     * Arahkan pengguna ke halaman otentikasi Google.
     */
    public function redirectToGoogle()
    {
        // Ganti 'smktelkom-lpg.sch.id' dengan domain Google Workspace-mu
        $parameters = [
            'prompt' => 'select_account',
            'hd' => 'smktelkom-lpg.sch.id'
        ];

        return Socialite::driver('google')
            ->with($parameters)
            ->redirect();
    }

    /**
     * Dapatkan informasi pengguna dari Google setelah login.
     */
    public function handleGoogleCallback()
    {
        try {
            // Ambil data user dari Google
            $googleUser = Socialite::driver('google')->user();

            // === LOGIKA INTI: CEK EMAIL ===
            $user = User::where('email', $googleUser->getEmail())->first();

            // 1. Jika user DITEMUKAN di database kita
            if ($user) {
                // Cek jika user terkait dengan pegawai (opsional, tapi bagus)
                if (!$user->employee) {
                    alert()->warning('Login Gagal', 'Akun Anda ditemukan tetapi tidak tertaut ke data pegawai.');
                    return redirect()->route('login');
                }

                // Langsung loginkan user tersebut
                Auth::login($user, true); // 'true' untuk "Remember Me"

                return redirect()->route('dashboard'); // Arahkan ke dashboard

            } else {
                // 2. Jika user TIDAK DITEMUKAN (sesuai permintaanmu)
                // Kita TIDAK buat user baru.
                alert()->error('Akses Ditolak', 'Email (' . $googleUser->getEmail() . ') tidak terdaftar di sistem. Silakan hubungi Administrator.');
                return redirect()->route('login');
            }
            // ==============================

        } catch (Exception $e) {
            // Tangani jika ada error (misal: user klik "cancel")
            Log::error('GOOGLE_SSO_ERROR: ' . $e->getMessage()); // Catat error
            alert()->error('Login Gagal', 'Terjadi kesalahan saat otentikasi dengan Google. Silakan coba lagi.');
            return redirect()->route('login');
        }
    }
}
