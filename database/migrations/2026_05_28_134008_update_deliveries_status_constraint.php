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
    DB::statement('ALTER TABLE deliveries DROP CONSTRAINT deliveries_status_check');
    
    DB::statement("
        ALTER TABLE deliveries 
        ADD CONSTRAINT deliveries_status_check 
        CHECK (status IN (
            'diproses',
            'selesai_dimasak',
            'dikirim',
            'selesai',
            'dibatalkan'
        ))
    ");
}

public function down(): void
{
    DB::statement('ALTER TABLE deliveries DROP CONSTRAINT deliveries_status_check');
    
    DB::statement("
        ALTER TABLE deliveries 
        ADD CONSTRAINT deliveries_status_check 
        CHECK (status IN (
            'diproses',
            'dikirim',
            'selesai',
            'dibatalkan'
        ))
    ");
}
};
