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
    DB::statement("ALTER TABLE orders DROP CONSTRAINT orders_payment_method_check");
    DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_payment_method_check CHECK (payment_method IN ('qris','bca','bri','transfer_bca','transfer_bri'))");
}

public function down(): void
{
    DB::statement("ALTER TABLE orders DROP CONSTRAINT orders_payment_method_check");
    DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_payment_method_check CHECK (payment_method IN ('qris','bca','bri'))");
}
};
