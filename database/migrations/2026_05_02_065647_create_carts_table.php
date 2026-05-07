<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel carts: header keranjang belanja.
     * Satu user hanya boleh punya satu cart ber-status 'active' sekaligus.
     * Saat checkout, status berubah dari 'active' → 'checkout'.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->enum('status', ['active', 'checkout'])->default('active');

            $table->timestamps();

            // Index: sering di-query berdasarkan user_id + status
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};