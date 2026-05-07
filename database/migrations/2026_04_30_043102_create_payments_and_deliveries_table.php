<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel payments dan deliveries sesuai ERD terbaru.
     *
     * Payments — perubahan dari ERD lama:
     * - Tambahan 'amount' (jumlah yang harus dibayar)
     * - Tambahan 'admin_note' (catatan dari admin saat verifikasi/tolak)
     * - Tambahan 'verified_by' (FK ke users — siapa admin yang verifikasi)
     * - Tambahan 'verified_at' dan 'paid_at' (timestamp terpisah)
     * - Status enum baru: pending, accepted, rejected
     *
     * Deliveries — perubahan dari ERD lama:
     * - Tambahan 'driver_id' (FK ke users — kurir/pengelola)
     * - Tambahan 'delivery_type' enum: pickup, delivery
     * - Status enum baru: pending, on_delivery, delivered
     * - Tambahan 'address' dan 'delivered_at'
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                  ->unique() // Satu order = satu payment
                  ->constrained('orders')
                  ->cascadeOnDelete();

            $table->string('payment_method', 50)->nullable();

            // Path file bukti transfer di storage (nullable sampai diupload)
            $table->string('payment_proof', 255)->nullable();

            // Jumlah yang harus dibayar (snapshot dari total_price order)
            $table->decimal('amount', 12, 2);

            $table->enum('status', ['pending', 'accepted', 'rejected'])
                  ->default('pending');

            // Catatan admin saat menerima atau menolak pembayaran
            $table->text('admin_note')->nullable();

            // Admin yang memverifikasi (nullable sampai diverifikasi)
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });

        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                  ->unique() // Satu order = satu delivery record
                  ->constrained('orders')
                  ->cascadeOnDelete();

            // Pengemudi/pengelola yang mengantarkan (nullable untuk pickup)
            $table->foreignId('driver_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->enum('delivery_type', ['pickup', 'delivery'])
                  ->default('pickup');

            $table->enum('status', ['pending', 'on_delivery', 'delivered'])
                  ->default('pending');

            // Alamat pengiriman (nullable untuk tipe pickup)
            $table->string('address', 255)->nullable();

            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('payments');
    }
};