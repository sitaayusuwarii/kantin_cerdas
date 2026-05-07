<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel orders sesuai ERD terbaru.
     *
     * Perubahan signifikan dari ERD lama:
     * - Kolom 'status' enum baru: baru, diproses, selesai, dibatalkan (Bahasa Indonesia)
     * - Tambahan 'order_number' (unik, format INV-YYYYMMDD-XXX)
     * - Tambahan 'pickup_schedule' (enum: istirahat_1, istirahat_2, pulang)
     * - Tambahan 'ordered_at' timestamp (bisa berbeda dari created_at)
     * - 'total_amount' diganti menjadi 'total_price'
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete();

            // Format: INV-20240115-001
            $table->string('order_number', 30)->unique();

            $table->decimal('total_price', 12, 2);

            $table->enum('status', ['baru', 'diproses', 'selesai', 'dibatalkan'])
                  ->default('baru');

            $table->enum('pickup_schedule', ['istirahat_1', 'istirahat_2', 'pulang'])
                  ->nullable();

            $table->text('note')->nullable();

            // Waktu order ditempatkan (bisa set manual, default = now())
            $table->timestamp('ordered_at')->useCurrent();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();

            $table->foreignId('menu_id')
                  ->constrained('menus')
                  ->restrictOnDelete();

            $table->unsignedSmallInteger('quantity');

            // Snapshot harga saat order dibuat (immutable — tidak berubah meski harga menu diubah)
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 12, 2);

            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};