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
        $table->enum('payment_method', ['qris', 'bca', 'bri'])->nullable();
        $table->string('payment_proof')->nullable();
        $table->enum('payment_status', ['pending', 'paid', 'rejected'])
              ->default('pending');
    });
}

public function down(): void
{
    Schema::table('carts', function (Blueprint $table) {
        $table->dropIndex('carts_user_status_index');
    });
}
};
