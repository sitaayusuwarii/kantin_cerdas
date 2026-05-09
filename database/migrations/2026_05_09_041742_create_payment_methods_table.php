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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();        // transfer_bca, qris, dll
            $table->string('name');                  // Transfer BCA
            $table->string('type');                  // bank_transfer, ewallet, qris, cash
            $table->string('account_number')->nullable(); // 1234567890
            $table->string('account_name')->nullable();   // a/n Kantin PAUD
            $table->string('logo_icon')->nullable();      // fa-solid fa-building-columns
            $table->text('instructions')->nullable();     // instruksi tambahan
            $table->string('qris_image')->nullable();     // path QRIS image
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
