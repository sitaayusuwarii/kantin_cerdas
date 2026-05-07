<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Skema menus sesuai ERD terbaru.
     *
     * Perbedaan dari ERD lama:
     * - Tidak ada tabel categories terpisah — category disimpan sebagai varchar
     * - Tambahan kolom: stock (int), total_sold (int)
     * - Tidak ada kolom slug (gunakan id sebagai route parameter)
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);

            // Stock dikelola dari backend; 0 = habis, otomatis tidak tersedia
            $table->unsignedInteger('stock')->default(0);

            $table->string('image', 255)->nullable();

            // Category disimpan sebagai string (denormalized), contoh: "Makanan", "Minuman"
            $table->string('category', 100)->nullable();

            $table->boolean('is_available')->default(true);

            // Counter total terjual untuk fitur "terlaris"
            $table->unsignedInteger('total_sold')->default(0);

            $table->timestamps();

            // Index untuk filter + sort yang sering dipakai
            $table->index(['is_available', 'category']);
            $table->index('total_sold');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};