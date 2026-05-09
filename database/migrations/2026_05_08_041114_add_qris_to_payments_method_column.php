<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE payments DROP CONSTRAINT payments_method_check");
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_method_check CHECK (method IN ('transfer_bri','transfer_bca','transfer_mandiri','gopay','ovo','dana','tunai','qris'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payments DROP CONSTRAINT payments_method_check");
        DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_method_check CHECK (method IN ('transfer_bri','transfer_bca','transfer_mandiri','gopay','ovo','dana','tunai'))");
    }
};