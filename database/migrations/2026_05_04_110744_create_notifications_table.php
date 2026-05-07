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
    Schema::create('notifications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('type'); // order_new, payment_new, stock_low
        $table->string('title');
        $table->string('message');
        $table->string('icon')->default('fa-bell');
        $table->string('color')->default('bg-forest-500');
        $table->string('url')->nullable();
        $table->timestamp('read_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
