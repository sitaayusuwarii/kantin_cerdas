<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('username', 50)->unique();
            
            // Tetap nullable agar proses Register kamu tidak error
            $table->string('full_name', 150)->nullable(); 
            $table->string('phone', 20)->nullable();
            $table->string('class', 20)->nullable();
            $table->string('password');

            $table->enum('role', ['admin', 'customer', 'pengelola'])
                  ->default('customer');

            $table->string('photo', 255)->nullable();
            
            // 👇 KITA TAMBAHKAN MANUAL KOLOM TELEGRAM TEMANMU DI SINI 👇
            $table->string('telegram_chat_id')->nullable();

            $table->rememberToken();
            $table->timestamps();

            $table->index('role');
        });

        // (Jika di file aslimu ada kode untuk create 'sessions' dan 'password_reset_tokens' di bawah sini, biarkan saja jangan dihapus ya)
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        
        // 👇 Tetap pertahankan kode rollback milikmu 👇
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};