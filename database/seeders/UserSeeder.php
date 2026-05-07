<?php
// ============================================================
// FILE: UserSeeder.php
// ============================================================
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Buat 3 akun demo:
     * 1. Admin
     * 2. Customer Siswa (dengan NIS, saldo Rp50.000)
     * 3. Customer Orang Tua (tanpa NIS, saldo Rp100.000)
     *
     * Setelah user dibuat, tambahkan beberapa favorit demo untuk customer.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@smartcanteen.test'],
            [
                'name'       => 'Administrator',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
                'balance'    => 0,
                'student_id' => null,
            ]
        );

        $budi = User::firstOrCreate(
            ['email' => 'budi@smartcanteen.test'],
            [
                'name'       => 'Budi Santoso',
                'password'   => Hash::make('password'),
                'role'       => 'customer',
                'balance'    => 50000.00,
                'student_id' => '2024001',
            ]
        );

        $siti = User::firstOrCreate(
            ['email' => 'siti@smartcanteen.test'],
            [
                'name'       => 'Siti Rahayu',
                'password'   => Hash::make('password'),
                'role'       => 'customer',
                'balance'    => 100000.00,
                'student_id' => null,
            ]
        );

        // Tambahkan menu favorit untuk akun demo customer
        // (hanya jika tabel menus sudah terisi oleh MenuSeeder)
        $menuIds = Menu::inRandomOrder()->limit(3)->pluck('id');

        if ($menuIds->isNotEmpty()) {
            $budi->favoriteMenus()->syncWithoutDetaching($menuIds->toArray());
            $siti->favoriteMenus()->syncWithoutDetaching(
                Menu::inRandomOrder()->limit(2)->pluck('id')->toArray()
            );
        }

        $this->command->info('✓ UserSeeder: 3 akun demo berhasil dibuat.');
        $this->command->line('  admin@smartcanteen.test  | password');
        $this->command->line('  budi@smartcanteen.test   | password');
        $this->command->line('  siti@smartcanteen.test   | password');
    }
}