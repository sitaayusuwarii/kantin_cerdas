<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel cart_items: detail item dalam sebuah keranjang.
     * Kolom 'note' untuk catatan per-item (contoh: "tidak pedas", "tambah saus").
     * Subtotal dihitung dari quantity × price menu saat item ditambahkan.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')
                  ->constrained('carts')
                  ->cascadeOnDelete();

            $table->foreignId('menu_id')
                  ->constrained('menus')
                  ->restrictOnDelete(); // Jangan hapus menu jika masih ada di cart

            $table->unsignedSmallInteger('quantity')->default(1);

            // Catatan khusus per-item dari customer
            $table->string('note', 255)->nullable();

            // Subtotal = quantity × harga menu SAAT INI (bukan snapshot)
            // Akan di-recalculate saat checkout untuk keamanan harga
            $table->decimal('subtotal', 12, 2)->default(0);

            $table->timestamps();

            // Satu cart tidak boleh punya item menu yang sama dua kali
            $table->unique(['cart_id', 'menu_id']);

            $table->index('cart_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};