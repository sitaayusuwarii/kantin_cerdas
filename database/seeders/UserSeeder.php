<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin (Dari kode temanmu)
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'full_name' => 'Administrator',
                'phone'     => '081234567890',
                'class'     => '-',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
            ]
        );

        // 2. Akun Pengelola (Dari kode temanmu)
        User::firstOrCreate(
            ['username' => 'kantin'],
            [
                'full_name' => 'Pengelola Kantin',
                'phone'     => '081111111111',
                'class'     => '-',
                'password'  => Hash::make('password'),
                'role'      => 'pengelola',
            ]
        );

        // 3. Akun Customer Demo (Diadaptasi dari kodemu agar sesuai ERD baru)
        $budi = User::firstOrCreate(
            ['username' => 'budi'],
            [
                'full_name' => 'Budi Santoso',
                'phone'     => '082222222222',
                'class'     => '10A',
                'password'  => Hash::make('password'),
                'role'      => 'customer',
            ]
        );

        // Tambahkan menu favorit untuk akun demo customer (Ide dari kodemu)
        // (Pastikan tabel menus sudah ada isinya)
        $menuIds = Menu::inRandomOrder()->limit(3)->pluck('id');

        if ($menuIds->isNotEmpty()) {
            $budi->favoriteMenus()->syncWithoutDetaching($menuIds->toArray());
        }

        // Tampilkan info di terminal saat di-seed
        $this->command->info('✓ UserSeeder: 3 akun demo berhasil dibuat.');
        $this->command->line('  admin  | password (Admin)');
        $this->command->line('  kantin | password (Pengelola)');
        $this->command->line('  budi   | password (Customer)');
    }
}