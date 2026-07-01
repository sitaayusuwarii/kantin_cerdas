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
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('menu_id')
                ->constrained('tenants')->onDelete('set null');
            $table->string('tenant_status')->default('baru')->after('tenant_id');
            // baru → diproses → selesai_dimasak
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'tenant_status']);
        });
    }
};
