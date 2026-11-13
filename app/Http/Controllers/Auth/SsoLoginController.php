<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SsoLoginController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        abort_unless(config('sso.client_id'), 503, 'Client ID SSO SISFO belum dikonfigurasi.');

        $state = Str::random(64);
        $verifier = $this->base64UrlEncode(random_bytes(64));
        $challenge = $this->base64UrlEncode(hash('sha256', $verifier, true));

        $request->session()->put([
            'sso_state' => $state,
            'sso_code_verifier' => $verifier,
        ]);

        $query = http_build_query([
            'client_id' => config('sso.client_id'),
            'redirect_uri' => config('sso.callback_url'),
            'response_type' => 'code',
            'scope' => config('sso.scope', 'profile:read'),
            'state' => $state,
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256',
        ], '', '&', PHP_QUERY_RFC3986);

        return redirect()->away(config('sso.base_url').'/oauth/authorize?'.$query);
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            return redirect()->route('login')->withErrors(['email' => 'Login dibatalkan atau akses SSO tidak disetujui.']);
        }

        $expectedState = $request->session()->pull('sso_state');
        $verifier = $request->session()->pull('sso_code_verifier');

        if (! $expectedState || ! hash_equals($expectedState, (string) $request->query('state')) || ! $verifier) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi SSO tidak valid atau sudah kedaluwarsa. Silakan coba lagi.']);
        }

        if (! $request->filled('code')) {
            return redirect()->route('login')->withErrors(['email' => 'Kode otorisasi tidak diterima dari SSO Stella.']);
        }

        try {
            $tokenResponse = Http::asForm()
                ->withoutVerifying()
                ->acceptJson()
                ->timeout(20)
                ->retry(2, 300, throw: false)
                ->post(config('sso.base_url').'/oauth/token', [
                    'grant_type' => 'authorization_code',
                    'client_id' => config('sso.client_id'),
                    'redirect_uri' => config('sso.callback_url'),
                    'code' => $request->string('code')->toString(),
                    'code_verifier' => $verifier,
                ]);

            if ($tokenResponse->failed() || ! $tokenResponse->json('access_token')) {
                Log::warning('Penukaran token SSO Stella gagal.', [
                    'status' => $tokenResponse->status(),
                    'response' => $tokenResponse->json(),
                ]);

                return redirect()->route('login')->withErrors(['email' => 'SSO Stella tidak dapat menyelesaikan proses autentikasi token.']);
            }

            $profileResponse = Http::withToken($tokenResponse->json('access_token'))
                ->withoutVerifying()
                ->acceptJson()
                ->timeout(20)
                ->retry(2, 300, throw: false)
                ->get(config('sso.base_url').'/api/sso/user');

            if ($profileResponse->failed()) {
                Log::warning('Pengambilan profil SSO Stella gagal.', [
                    'status' => $profileResponse->status(),
                    'response' => $profileResponse->body(),
                ]);

                $message = match ($profileResponse->status()) {
                    401 => 'Token ditolak saat mengambil data profil dari SSO.',
                    403 => 'Akun berhasil masuk, tetapi tidak memiliki izin profile:read.',
                    default => 'Profil pengguna tidak dapat diambil dari SSO Stella.',
                };

                return redirect()->route('login')->withErrors(['email' => $message]);
            }

            $profile = $profileResponse->json();
            $email = Str::lower((string) ($profile['email'] ?? ''));

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->route('login')->withErrors(['email' => 'Profil SSO tidak memiliki alamat email yang valid.']);
            }

            $user = User::where('email', $email)->first();

            if (! $user) {
                return redirect()->route('login')->withErrors(['email' => "Email SSO ({$email}) tidak terdaftar di Manajemen Aset. Hubungi administrator."]);
            }

            if (! $user->employee && $user->role !== 'admin') {
                return redirect()->route('login')->withErrors(['email' => 'Akun Anda belum tertaut ke data pegawai Manajemen Aset.']);
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        } catch (\Throwable $exception) {
            report($exception);
            return redirect()->route('login')->withErrors(['email' => 'Terjadi kendala saat memproses login SSO: ' . $exception->getMessage()]);
        }
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
