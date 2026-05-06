<?php

namespace App\Http\Controllers;

use App\Models\DigitalDocument;
use App\Models\UserDigitalSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DigitalSignatureController extends Controller
{
    public function index()
    {
        $user       = Auth::user();
        $signature  = UserDigitalSignature::where('user_id', $user->id)->first();
        $documents  = DigitalDocument::where('signed_by', $user->id)
            ->orderByDesc('signed_at')
            ->paginate(10);
        $validCount = DigitalDocument::where('signed_by', $user->id)->where('is_valid', true)->count();

        return view('pages.tanda-tangan.index', compact('signature', 'documents', 'validCount'));
    }

    public function setup(Request $request)
    {
        $request->validate([
            'ttd_image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'pin'              => 'nullable|digits_between:4,8|confirmed',
            'pin_confirmation' => 'nullable',
        ]);

        $signature = UserDigitalSignature::firstOrNew(['user_id' => Auth::id()]);

        if ($request->hasFile('ttd_image')) {
            if ($signature->ttd_image_path) {
                Storage::disk('public')->delete($signature->ttd_image_path);
            }
            $signature->ttd_image_path = $request->file('ttd_image')->store('tanda-tangan', 'public');
        }

        if ($request->filled('pin')) {
            $signature->pin_hash = Hash::make($request->pin);
        }

        $signature->is_active = true;
        $signature->save();

        return back()->with('success', 'Tanda tangan digital berhasil diperbarui.');
    }

    /**
     * Import gambar tanda tangan dari SISFO via API.
     */
    public function importFromSisfo(Request $request)
    {
        $request->validate([
            'sisfo_email'    => 'required|email',
            'sisfo_password' => 'required|string',
        ]);

        $sisfoUrl = rtrim(config('services.sisfo.url', 'https://sisfo.smktelkom-lpg.id'), '/');

        // Login ke SISFO
        $loginRes = Http::post("{$sisfoUrl}/api/login", [
            'email'       => $request->sisfo_email,
            'password'    => $request->sisfo_password,
            'device_name' => 'sarpra-import',
        ]);

        if (!$loginRes->successful() || !$loginRes->json('token')) {
            return back()->with('error', 'Login ke SISFO gagal. Periksa email dan password.');
        }

        $sisfoToken = $loginRes->json('token');

        // Ambil URL gambar tanda tangan dari SISFO
        $sigRes = Http::withToken($sisfoToken)
            ->get("{$sisfoUrl}/api/signature/my-image");

        if (!$sigRes->successful() || !$sigRes->json('image_url')) {
            return back()->with('error', 'Gagal mengambil tanda tangan dari SISFO. Pastikan sudah setup tanda tangan di SISFO terlebih dahulu.');
        }

        $imageUrl = $sigRes->json('image_url');

        // Download gambar dan simpan lokal
        $imgContent = Http::get($imageUrl);
        if (!$imgContent->successful()) {
            return back()->with('error', 'Gagal mengunduh gambar tanda tangan dari SISFO.');
        }

        $signature = UserDigitalSignature::firstOrNew(['user_id' => Auth::id()]);

        if ($signature->ttd_image_path) {
            Storage::disk('public')->delete($signature->ttd_image_path);
        }

        $ext      = 'png';
        $filename = 'tanda-tangan/' . Auth::id() . '_sisfo_import.' . $ext;
        Storage::disk('public')->put($filename, $imgContent->body());

        $signature->ttd_image_path = $filename;
        $signature->is_active      = true;
        $signature->save();

        return back()->with('success', 'Gambar tanda tangan berhasil diimpor dari SISFO.');
    }

    public function revoke(Request $request)
    {
        $request->validate([
            'token'         => 'required|string|exists:digital_documents,token',
            'pin'           => 'required|string',
            'revoke_reason' => 'nullable|string|max:255',
        ]);

        $user      = Auth::user();
        $signature = UserDigitalSignature::where('user_id', $user->id)->first();

        if (!$signature || !$signature->verifyPin($request->pin)) {
            return back()->with('error', 'PIN salah. Pencabutan gagal.');
        }

        $doc = DigitalDocument::where('token', $request->token)
            ->where('signed_by', $user->id)
            ->firstOrFail();

        $doc->update([
            'is_valid'      => false,
            'revoked_at'    => now(),
            'revoke_reason' => $request->revoke_reason ?: 'Dicabut oleh penandatangan.',
        ]);

        return back()->with('success', 'Tanda tangan berhasil dicabut.');
    }

    public function revokeSelected(Request $request)
    {
        $request->validate([
            'pin'           => 'required|string',
            'tokens'        => 'required|array|min:1',
            'tokens.*'      => 'required|string|exists:digital_documents,token',
            'revoke_reason' => 'nullable|string|max:255',
        ]);

        $user      = Auth::user();
        $signature = UserDigitalSignature::where('user_id', $user->id)->first();

        if (!$signature || !$signature->verifyPin($request->pin)) {
            return back()->with('error', 'PIN salah. Pencabutan gagal.');
        }

        $count = DigitalDocument::whereIn('token', $request->tokens)
            ->where('signed_by', $user->id)
            ->where('is_valid', true)
            ->update([
                'is_valid'      => false,
                'revoked_at'    => now(),
                'revoke_reason' => $request->revoke_reason ?: 'Dicabut massal oleh penandatangan.',
            ]);

        return back()->with('success', "{$count} tanda tangan berhasil dicabut.");
    }

    public function revokeAll(Request $request)
    {
        $request->validate([
            'pin'           => 'required|string',
            'revoke_reason' => 'nullable|string|max:255',
        ]);

        $user      = Auth::user();
        $signature = UserDigitalSignature::where('user_id', $user->id)->first();

        if (!$signature || !$signature->verifyPin($request->pin)) {
            return back()->with('error', 'PIN salah. Pencabutan gagal.');
        }

        $count = DigitalDocument::where('signed_by', $user->id)
            ->where('is_valid', true)
            ->update([
                'is_valid'      => false,
                'revoked_at'    => now(),
                'revoke_reason' => $request->revoke_reason ?: 'Dicabut semua oleh penandatangan.',
            ]);

        return back()->with('success', "Semua {$count} tanda tangan berhasil dicabut.");
    }

    /**
     * Endpoint publik untuk verifikasi token tanda tangan.
     */
    public function verifyPublic(string $token)
    {
        $doc = DigitalDocument::where('token', $token)->first();
        return view('public.signature-verify', compact('doc', 'token'));
    }
}
