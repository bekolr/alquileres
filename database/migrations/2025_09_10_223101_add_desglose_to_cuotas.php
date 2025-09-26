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
        Schema::table('cuotas', function (Blueprint $table) {
            //
             $table->decimal('monto_alquiler', 12, 2)->default(0);
            $table->decimal('monto_expensas', 12, 2)->default(0);
            $table->decimal('monto_comision', 12, 2)->default(0);
            $table->decimal('monto_deposito', 12, 2)->default(0);
            $table->decimal('monto_total', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuotas', function (Blueprint $table) {
            //
            dropColumn('monto_alquiler');
            dropColumn('monto_expensas');    
            dropColumn('monto_comision');
            dropColumn('monto_deposito');
            dropColumn('monto_total');
            
        });
    }
};
