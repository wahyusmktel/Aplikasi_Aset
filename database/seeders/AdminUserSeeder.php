<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat satu akun admin default
        User::create([
            'name' => 'Admin',
            'email' => 'admin@aset.com',
            'password' => Hash::make('password'), // Ganti 'password' dengan password yang lebih aman
            'role' => 'admin',
        ]);
    }
}
