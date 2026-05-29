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
    Schema::table('deliveries', function (Blueprint $table) {
        $table->timestamp('cooked_at')->nullable()->after('processed_at');
    });
}

public function down(): void
{
    Schema::table('deliveries', function (Blueprint $table) {
        $table->dropColumn('cooked_at');
    });
}
};
