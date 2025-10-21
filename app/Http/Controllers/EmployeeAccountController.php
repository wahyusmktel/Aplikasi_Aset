<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;

class EmployeeAccountController extends Controller
{
    /**
     * Tampilkan form pembuatan akun untuk pegawai.
     */
    public function create(Employee $employee)
    {
        // Pastikan pegawai belum punya akun
        if ($employee->user_id) {
            alert()->info('Info', 'Pegawai ini sudah memiliki akun.');
            return redirect()->route('employees.index');
        }
        return view('employees.accounts.create', compact('employee'));
    }

    /**
     * Simpan akun user baru dan link ke pegawai.
     */
    public function store(Request $request, Employee $employee)
    {
        // Pastikan pegawai belum punya akun (double check)
        if ($employee->user_id) {
            alert()->error('Gagal', 'Pegawai ini sudah memiliki akun.');
            return redirect()->route('employees.index');
        }

        $request->validate([
            'name' => 'required|string|max:255', // Nama user (bisa beda dikit dari nama pegawai)
            'email' => 'required|string|email|max:255|unique:' . User::class, // Email harus unik di tabel users
            'password' => ['required', 'confirmed', Rules\Password::defaults()], // Password + konfirmasi
        ]);

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role untuk pegawai adalah 'user'
        ]);

        // Link user baru ke data pegawai
        $employee->update(['user_id' => $user->id]);

        alert()->success('Berhasil!', 'Akun login untuk ' . $employee->name . ' berhasil dibuat.');
        return redirect()->route('employees.index');
    }

    // public function destroy(User $user) { ... } // Logika hapus akun jika diperlukan

    /**
     * Tampilkan form reset password untuk akun user pegawai.
     */
    public function showResetPasswordForm(User $user)
    {
        // Pastikan user ini terkait dengan seorang pegawai
        if (!$user->employee) {
            alert()->error('Gagal', 'Akun ini tidak terkait dengan data pegawai.');
            return redirect()->route('employees.index');
        }
        return view('employees.accounts.reset-password', compact('user'));
    }

    /**
     * Update password untuk akun user pegawai.
     */
    public function updatePassword(Request $request, User $user)
    {
        // Pastikan user ini terkait dengan seorang pegawai
        if (!$user->employee) {
            alert()->error('Gagal', 'Akun ini tidak terkait dengan data pegawai.');
            return redirect()->route('employees.index');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        alert()->success('Berhasil!', 'Password untuk akun ' . $user->name . ' berhasil direset.');
        return redirect()->route('employees.index');
    }


    /**
     * Hapus akun user dan putuskan link dari pegawai.
     */
    public function destroy(User $user)
    {
        // Pastikan user ini terkait dengan seorang pegawai (penting!)
        if (!$user->employee) {
            alert()->error('Gagal', 'Akun ini tidak terkait dengan data pegawai.');
            return redirect()->route('employees.index');
        }

        // Gunakan transaksi database untuk keamanan
        DB::beginTransaction();
        try {
            // 1. Putuskan link dari employee
            $employee = $user->employee;
            $employee->update(['user_id' => null]);

            // 2. Hapus user
            $user->delete();

            DB::commit(); // Konfirmasi semua perubahan jika berhasil

            alert()->success('Berhasil!', 'Akun login ' . $user->name . ' berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika ada error
            alert()->error('Gagal!', 'Terjadi kesalahan saat menghapus akun: ' . $e->getMessage());
        }

        return redirect()->route('employees.index');
    }
}
