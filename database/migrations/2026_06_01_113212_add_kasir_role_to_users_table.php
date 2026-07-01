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
    // PostgreSQL perlu drop constraint lama dulu lalu buat baru
    DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
    DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check 
        CHECK (role IN ('admin', 'pengelola', 'kasir', 'customer'))");
}

public function down(): void
{
    DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
    DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check 
        CHECK (role IN ('admin', 'pengelola', 'customer'))");
}
};
