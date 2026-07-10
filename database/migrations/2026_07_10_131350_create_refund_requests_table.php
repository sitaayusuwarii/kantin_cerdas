<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->enum('status', ['menunggu_info', 'menunggu_transfer', 'selesai'])
                  ->default('menunggu_info');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_requests');
    }
};