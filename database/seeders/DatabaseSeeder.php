<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin Utama
        User::create([
            'username'   => 'admin_utama',
            'full_name'  => 'Super Admin',
            'phone'      => '081111111111',
            'class'      => null, // Admin tidak punya kelas
            'password'   => Hash::make('password123'), 
            'role'       => 'admin',
            'balance'    => 0,
            'student_id' => null,
            'photo'      => null,
        ]);

        // 2. Akun Pengelola Kantin (Sesuai ERD baru: 'pengelola')
        User::create([
            'username'   => 'pengelola_kantin',
            'full_name'  => 'Ibu Kantin',
            'phone'      => '082222222222',
            'class'      => null,
            'password'   => Hash::make('password123'),
            'role'       => 'pengelola', // Menggunakan 'pengelola' sesuai ERD
            'balance'    => 0,
            'student_id' => null,
            'photo'      => null,
        ]);

        // 3. Akun Customer (Siswa)
        User::create([
            'username'   => 'cahya',
            'full_name'  => 'Cahya Darma',
            'phone'      => '083333333333',
            'class'      => '6 TRPL', // Contoh kelas yang diisi
            'password'   => Hash::make('password123'),
            'role'       => 'customer',
            'balance'    => 100000, // Saldo awal untuk testing pesanan
            'student_id' => '230010020',
            'photo'      => null,
        ]);
    }
}