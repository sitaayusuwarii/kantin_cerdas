<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete();

            $table->decimal('amount', 10, 2);

            $table->enum('method', [
                'transfer_bri',
                'transfer_bca',
                'transfer_mandiri',
                'gopay',
                'ovo',
                'dana',
                'tunai',
            ]);

            // Path file bukti transfer
            $table->string('proof_path')->nullable();

            $table->enum('status', [
                'menunggu',      // baru diupload, belum dicek
                'terverifikasi', // pengelola sudah konfirmasi
                'ditolak',       // bukti tidak valid
            ])->default('menunggu');

            $table->text('note')->nullable();           // catatan siswa
            $table->text('rejection_reason')->nullable(); // alasan ditolak

            // Siapa & kapan diverifikasi
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
