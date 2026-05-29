<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tipe pesanan: dine_in (makan di tempat) | takeaway (bawa pulang) | walkin (langsung di kantin tanpa pre-order)
            $table->string('order_type')->default('takeaway')->after('note');

            // Nomor meja — hanya diisi kalau order_type = dine_in
            $table->string('table_number')->nullable()->after('order_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'table_number']);
        });
    }
};