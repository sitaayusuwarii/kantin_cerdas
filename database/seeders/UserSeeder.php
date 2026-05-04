<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'full_name' => 'Administrator',
            'phone' => '081234567890',
            'class' => '-',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'username' => 'kantin',
            'full_name' => 'Pengelola Kantin',
            'phone' => '081111111111',
            'class' => '-',
            'password' => Hash::make('password'),
            'role' => 'pengelola',
        ]);
    }
}