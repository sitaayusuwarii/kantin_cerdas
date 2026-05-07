<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan: php artisan migrate
     * Tambah kolom status ke tabel users agar suspend benar-benar bersih.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'suspended', 'inactive'])
                  ->default('active')
                  ->after('balance');

            // Opsional: kolom kelas untuk siswa/customer
            $table->string('kelas', 100)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'kelas']);
        });
    }
};