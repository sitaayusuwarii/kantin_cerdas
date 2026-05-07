<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel favorites: relasi many-to-many user ↔ menu.
     * Tidak menggunakan pivot timestamps (ERD tidak mencantumkannya).
     */
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('menu_id')
                  ->constrained('menus')
                  ->cascadeOnDelete();

            $table->timestamps();

            // Satu user hanya bisa favorit satu menu sekali
            $table->unique(['user_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};