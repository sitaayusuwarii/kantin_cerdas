<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete();

            // Nomor pesanan: SC-001, SC-002, dst
            $table->string('order_number', 20)->unique();

            $table->enum('status', [
                'baru',
                'dikonfirmasi',
                'diproses',
                'dikirim',
                'selesai',
                'dibatalkan',
            ])->default('baru');

            $table->enum('pickup_schedule', [
                'istirahat_1',   // 09:30
                'istirahat_2',   // 12:00
                'pulang',        // 14:30
            ])->default('istirahat_1');

            $table->text('note')->nullable();

            $table->decimal('total_price', 10, 2)->default(0);

            // Timestamps aksi
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Siapa pengelola yang konfirmasi
            $table->foreignId('confirmed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Index untuk query umum
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
