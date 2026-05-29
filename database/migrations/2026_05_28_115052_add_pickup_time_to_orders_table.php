<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Jam bebas untuk takeaway, misal "10:30"
            $table->string('pickup_time')->nullable()->after('order_type');

            // Kelas tujuan untuk delivery — default dari users.class, bisa diedit
            $table->string('classroom')->nullable()->after('pickup_time');
        });

        // Ubah pickup_schedule jadi nullable
        // (takeaway tidak pakai slot, jadi boleh null)
        Schema::table('orders', function (Blueprint $table) {
            $table->string('pickup_schedule')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['pickup_time', 'classroom']);
            $table->string('pickup_schedule')->nullable(false)->change();
        });
    }
};