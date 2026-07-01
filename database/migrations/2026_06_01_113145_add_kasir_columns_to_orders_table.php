<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->string('order_source')->default('customer')->after('order_type');
        // nilai: 'customer' | 'kasir'
        $table->foreignId('kasir_id')->nullable()->after('order_source')
              ->constrained('users')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropForeign(['kasir_id']);
        $table->dropColumn(['order_source', 'kasir_id']);
    });
}
};
