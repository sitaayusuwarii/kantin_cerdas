<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Skema users sesuai ERD terbaru.
     *
     * CATATAN PENTING:
     * ERD baru tidak menggunakan kolom 'email'. Jika Anda ingin tetap menggunakan
     * Auth bawaan Laravel yang berbasis email, ada dua opsi:
     *   (a) Tetap simpan kolom email sebagai nullable/opsional.
     *   (b) Override method username() di model User dan ubah LoginRequest.
     *
     * File ini mengikuti ERD 100%: login menggunakan 'username'.
     * Sesuaikan AuthController dan LoginRequest untuk menggunakan 'username'.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('username', 50)->unique();
            $table->string('full_name', 150)->nullable();
            $table->string('phone', 20)->nullable();

            // Kelas siswa, contoh: "10A", "11 IPA 2" — nullable untuk akun non-siswa
            $table->string('class', 20)->nullable();

            $table->string('password');

            $table->enum('role', ['admin', 'customer', 'pengelola'])
                  ->default('customer');

            // Path foto profil di storage
            $table->string('photo', 255)->nullable();

            $table->rememberToken();
            $table->timestamps();

            $table->index('role');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        // 👇 Jangan lupa tambahkan ini juga untuk rollback 👇
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};