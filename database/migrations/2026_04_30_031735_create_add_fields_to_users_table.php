<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom balance dan student_id ke tabel users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom ini ditambahkan karena belum ada di create_users_table
            $table->decimal('balance', 12, 2)
                  ->default(0)
                  ->after('role');

            $table->string('student_id', 20)
                  ->nullable()
                  ->unique()
                  ->after('balance')
                  ->comment('NIS/NISN untuk siswa, nullable untuk orang tua');
        });
    }

    /**
     * Rollback: hapus kolom yang ditambahkan.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'balance', 
                'student_id'
            ]);
        });
    }
};